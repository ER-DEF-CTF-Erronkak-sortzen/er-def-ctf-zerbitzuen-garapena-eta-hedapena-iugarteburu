#!/usr/bin/env python3

from ctf_gameserver import checkerlib
import logging
import http.client
import socket
import paramiko
import hashlib
PORT_WEB = 8080
PORT_SSH = 2222
def ssh_connect():
    def decorator(func):
        def wrapper(*args, **kwargs):
            # SSH connection setup
            client = paramiko.SSHClient()
            client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            client.connect(args[0].ip, username = 'proba', port=PORT_SSH, password='xza')

            # Call the decorated function with the client parameter
            args[0].client = client
            result = func(*args, **kwargs)

            # SSH connection cleanup
            client.close()
            return result
        return wrapper
    return decorator

class MyChecker(checkerlib.BaseChecker):

    def __init__(self, ip, team):
        checkerlib.BaseChecker.__init__(self, ip, team)
        self._baseurl = f'http://[{self.ip}]:{PORT_WEB}'
        logging.info(f"URL: {self._baseurl}")

    @ssh_connect()
    def place_flag(self, tick):
        flag = checkerlib.get_flag(tick)
        creds = self._add_new_flag(self.client, flag)
        logging.info(f'creds: {creds}')
        if not creds:
            return checkerlib.CheckResult.FAULTY
        logging.info('created')
        checkerlib.store_state(str(tick), creds)
        checkerlib.set_flagid(str(tick))
        return checkerlib.CheckResult.OK

    def check_service(self):
        # check if ports are open
        if not self._check_port_web(self.ip, PORT_WEB) or not self._check_port_ssh(self.ip, PORT_SSH):
            return checkerlib.CheckResult.DOWN
        # check if server is Apache 2.4.54
        #if not self._check_apache_version():
        #    return checkerlib.CheckResult.FAULTY
        # check if irudiak.php still has the code to upload files
        file_path_web = '/var/www/html/irudiak.php'
        if not self._check_uploading_integrity(file_path_web):
            return checkerlib.CheckResult.FAULTY            
        # check if /etc/sshd_config from konektatu_web has been changed by comparing its hash with the hash of the original file
        file_path_ssh = '/etc/ssh/sshd_config'
        if not self._check_ssh_integrity(file_path_ssh):
            return checkerlib.CheckResult.FAULTY 
        # check if uploads folder permissions have been changed
        if not self._check_uploads_folder():
            return checkerlib.CheckResult.FAULTY     
        return checkerlib.CheckResult.OK
    
    def check_flag(self, tick):
        if not self.check_service():
            return checkerlib.CheckResult.DOWN
        flag = checkerlib.get_flag(tick)
        flag_present = self._check_flag_present(flag)
        if not flag_present:
            return checkerlib.CheckResult.FLAG_NOT_FOUND
        return checkerlib.CheckResult.OK
      
    @ssh_connect()
    def _check_uploading_integrity(self, path):
        ssh_session = self.client
        command = f"cat {path}| grep 'move_uploaded_file'"
        stdin, stdout, stderr = ssh_session.exec_command(command)

        if stdout:
            return True
        else:
            return False
    
    @ssh_connect()
    def _check_ssh_integrity(self, path):
        ssh_session = self.client
        command = f"cat {path}"
        stdin, stdout, stderr = ssh_session.exec_command(command)
        if stderr.channel.recv_exit_status() != 0:
            return False
        output = stdout.read().decode().strip()
        print (hashlib.md5(output.encode()).hexdigest())

        return hashlib.md5(output.encode()).hexdigest() == 'a38e22a50d260179a6cc7caaa88e85e6'
    
    @ssh_connect()
    def _check_uploads_folder(self):
        ssh_session = self.client
        command = f"ls -ld /var/www/html/uploads/ | grep 'drwxrwxrwx . www-data www-data'"

        stdin, stdout, stderr = ssh_session.exec_command(command)
        if stderr.channel.recv_exit_status() != 0:
            return False
        output = stdout.read().decode().strip()
        #Hutxik dagoen edo ez itzuli
        return bool(output)
    
    # Private Funcs - Return False if error
    def _add_new_flag(self, ssh_session, flag):
        # Execute the file creation command in the container
        command = f"echo {flag} >> /tmp/flag.txt"
        stdin, stdout, stderr = ssh_session.exec_command(command)

        # Check if the command executed successfully
        if stderr.channel.recv_exit_status() != 0:
            return False
        
        # Return the result
        return {'flag': flag}

    @ssh_connect()
    def _check_flag_present(self, flag):
        ssh_session = self.client
        command = f"grep {flag} /tmp/flag.txt"
        stdin, stdout, stderr = ssh_session.exec_command(command)
        if stderr.channel.recv_exit_status() != 0:
            return False

        output = stdout.read().decode().strip()
        return flag == output

    def _check_port_web(self, ip, port):
        try:
            conn = http.client.HTTPConnection(ip, port, timeout=5)
            conn.request("GET", "/")
            response = conn.getresponse()
            return response.status == 200
        except (http.client.HTTPException, socket.error) as e:
            print(f"Exception: {e}")
            return False
        finally:
            if conn:
                conn.close()

    def _check_port_ssh(self, ip, port):
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(5)
            result = sock.connect_ex((ip, port))
            return result == 0
        except socket.error as e:
            print(f"Exception: {e}")
            return False
        finally:
            sock.close()

    @ssh_connect()
    def _check_apache_version(self):
        ssh_session = self.client
        command = f"apache2 -v | grep 'Apache/2.4.54'"
        stdin, stdout, stderr = ssh_session.exec_command(command)

        if stdout:
            return True
        else:
            return False
  
if __name__ == '__main__':
    checkerlib.run_check(MyChecker)




