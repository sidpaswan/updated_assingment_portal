For Setup:
1. There are 3 kinds of files, PHPFiles, Bash scripts(SHScripts) and Textfiles for configuration and password/assignment storage.

2. Open the setup.conf file to configure the File Storage location of submission files and server files, you can also mention the course code in there only. (Make sure to change the $base_dir according to your server)

3. Just run the ./init.sh script and it will take care of copying the necessary files to correct places (PHPfile to /var/www/html/, SHScripts and AssignList, PasswordList to internal $base_dir directory). 

4. Open your browser and type the URL: localhost/$course/submission/ ($course = coursecode mentioned in setup.conf)

5. rollList.txt contains the list of student users and their respective email ID, seperated by commas and 'admin' is the username for Faculty, TA whose password you set during init.sh.

6. Update the "plist.txt" with the leaked password dictionary, just open the file and add the passwords.

7.To setup email for forgot password go to "PHPfiles/resetPass.php" and on line 17 and 18 replace <Your Email Here> with the outlook email ID <Password Here> with the password for the email. You can use gmail also instead of an outlook account.
