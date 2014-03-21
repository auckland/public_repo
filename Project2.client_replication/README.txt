Developed by Sergio Zalyubovskiy (C) 2012

Description of the project:
---------------------------
This Project was intended to make a massive upload of clients database from physical TXT-like plain files at user or system call.
The data was generated in special format and extracted from Informix running database.
Constantly running crontab process was in charge of detection if there's any new files in Source folder.
After each time new files were generated, replication process was started. 

The Circuit:
------------
1. The Shell executable "vuelco.sh" was invoked by User from Linux Terminal.
2. From this Shell user had to select to invoke the PHP "vuelco_clientes.php"
3. This executable "vuelco_clientes.php" had to take the information from the bulk data file "CLIENTES_PRS.TXT.org" 
4. Message had to be generated using the Cobol COPY file "clivuel_pfis.cpy" for formatting and message template 
   file "alta_cliente.msg" to generate formatted message and invoke another Cobol program.
4. Parsing the bulk file "CLIENTES_PRS.TXT.org" this program "vuelco_clientes.php" had to replicate client's data one-by-one.
5. Stored in ./tmp folder formatted files were used then by other Cobol process and deleted after they were processed.