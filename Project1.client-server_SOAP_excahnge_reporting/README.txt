Developed by Sergio Zalyubovskiy (C) 2012

Description of the project:
---------------------------
This project was created with an objective to generate and send information to the Governmental Revenue Administration Office (Argentina).

Working Circuit:
----------------
1. Receive information from the Web Front-End.
2. Send it to the Cobol Back-End with sys or exec (some previous formatting is required).
3. Receive the result from Cobol Application, which has already saved the information in internal Banking database.
4. Send it with secured SOAP channel (WS) to validation Authority in Revenue Office in XML format.
5. Save sent XML Request.
6. Receive the answer with SOAP from the Revenue Office.
7. Store the XML Response.
8. Call the Cobol program and send the final result in formatted message attaching path to saved XMLs.
9. Show the result to Front-End user. 
Basically, operation could've been Denied, Approved or Pending with reference number received from  Banking database and message of Revenue Office.

Technologies used:
------------------
Linux CentOS 5.6 Server.
OpenSSL - http://www.openssl.org/ - Required for SSL client transport and WS-Security with Apache Rampart/C
Apache2 httpd - http://httpd.apache.org/ - Required for deploying services with HTTPD
Libxml2 - http://www.xmlsoft.org/
libiconv - http://www.gnu.org/software/libiconv/
zlib - http://www.zlib.net/
iksemel - http://iksemel.jabberstudio.org/ - Required for XMPP transport

Process:
--------
1. Download and compile wso2-wsf-php-src-1.2.0.tar.gz
2. ZEND server required.
3. Use wsfphp.spec to run rpmbuild. Use SVN Repo https://svn.wso2.org/repos/contrib/wsf/php/trunk/build/packaging/rpm/wsfphp.spec
4. Install compiled RPM x86_64/wso2-wsf-php-2.1.0-1.x86_64.rpm (name could differ)

Short installation order:
-------------------------
1. Install php-xml.x86_64 with yum
2. Download wso2-wsf-php-src-2.1.0.tar.gz from http://wso2.org
3. Extract wso2-wsf-php-src-2.1.0.tar.gz
4. Compile and install with GCC
5. Copy xsl.ini wsf.ini to php.d
6. Edit wsf.ini enabling wsf.so extension 
7. Set directories and log level (depends on OS)
	wsf.home as “/usr/lib64/php/modules/wsf_c"
	wsf.log_path as “/usr/lib64/php/modules/wsf_c/logs"
	wsf.log_level at Level 3