# Service definition:
- Docker bakarra daukatu:  Apache zerbitzu bat du martxan
Erasotzaileak web orrialde batera sarbidea du. Bertako argazkiak orrrian fitxategiak igotzeko aukera dauka. 
Fitxategiak ez daude filtratuta, beraz reverse shell bat ahalbidetzen duen fitxategi bat igo dezake. 
Reverse shellaren bidez makinan bertan dauden flagak eskuratu ahalko ditu.

# Service implementation:
Web dokerra inplementatuta dagoen moduan 3 .php fitxategi hartzen ditu makinatik, eta '/var/www/html/' karpetara kopiatu. Horietatik interesatzen zaiguna irudiak.php da. 
Webguneak momentuoro utzi beharko du fitxategiak igotzen.
Flagak sartu ahal izateko ssh zerbitzu bat ere izango du martxan.
 
-Flags: 
    Flagak konektatu_web_1 dockerraren '/tmp/flags.txt' fitxategian daude. 

# About exploting:
- ERASOTZAILEAK: Webgunearen menuko 'Argazkiak' aukerara joanda, erabiltzaileek fitxategiak formulario baten bidez igotzea ahalbidetzen du. Igotako fitxategiak web zerbitzariaren direktorio batean gordetzen dira (/uploads). Erabiltzaileak reverse-shell bat irekitzen duen fitxategi bat igoko du eta erasotzeko darabilen makinara konektatu. Era honetan erasotzaileak /tmp/flags.txt-tik flagak lortuko ditu.

- DEFENDATZAILEAK: 
    - Baimendutako fitxategi motak mugatu, exekutagarriak ez diren formatuetara (adibidez, irudiak bakarrik: .jpg, .png, .gif). Irudiak.php artxiboan begiratu daiteke ea igotako fitxategiaren luzapena baimendutakoen artean dagoen uploads karpetara igo aurretik. 
    - MIME mota eta fitxategiaren benetako edukia balioztatu, ziurtatzeko luzapen deklaratuarekin bat datorrela.
    -	Igoera-karpetan script-en exekuzioa desgaitu, zerbitzariaren konfigurazioa egokituz (virtualhostak edo .htaccess aldatuta)


  Attack performed by Team1 against Team 2. 
  Inspect web page in 10.0.2.101, go to Argazkiak in the menu and upload a reverse_shell script
  Open a listener in the attacker machine
  cat /tmp/flags.txt
     Copy last flags
     Exit
  'ssh -i /home/isard/.ssh/id_rsa root@10.0.1.1'
  nano /root/xxx.flag
    Paste copied flags. 

  Defense performed by Team2
     'ssh root@10.0.2.101'
     docker exec -it pasapasa_web_1 /bin/bash
     nano /etc/apache2/sites-available/000-default.conf
  And add:
    <Directory /var/www/html/uploads>
      # PHP exekutatzeko desgaitu
      php_admin_flag engine Off
      # CGI script-ak exekutatzea debekatu
      Options -ExecCGI
      AllowOverride None
      Require all granted
    </Directory>
    /etc/init.d/apache2 reload


# Checker checks:
- Ports to reach dockers are open (WEB:8080; SSH 2222)
- /var/www/html/irudiak.php file still has the code to upload files
- /etc/sshd_config  file's content from konektatu_web docker has not been changed. (ssh is necessary to upload flags)
- check if uploads folder permissions have been changed (ls -ld)
- apache version has not changed

Checks done: 
- TEAM 1. Stop the container: 'root@team0-services:~# 'docker stop konektatu_web_1' It works OK, service's status becomes DOWN. 
- TEAM 1. Change '/var/www/html/irudiak.php' file from 'konektatu_web' docker, removing the option to upload files. It works OK, service's status becomes faulty. 
- TEAM 2. 'ssh service stop'. It works OK, service's status becomes faulty. 
- TEAM 2. chmod 444 /var/www/html/uploads. It works OK, service's status becomes faulty. 
- TEAM 1. apt upgrade apache2. It works OK, service's status becomes faulty. 

# License notes
Parts from:
https://github.com/kristianvld/SQL-Injection-Playground



