@echo off
rem START or STOP Services
rem ----------------------------------
rem Check if argument is STOP or START

if not ""%1"" == ""START"" goto stop

if exist C:\secret\secretsecret\Xampp\hypersonic\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\server\hsql-sample-database\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\ingres\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\ingres\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\mysql\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\mysql\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\postgresql\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\postgresql\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\apache\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\apache\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\openoffice\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\openoffice\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\apache-tomcat\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\apache-tomcat\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\resin\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\resin\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\jetty\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\jetty\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\subversion\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\subversion\scripts\ctl.bat START)
rem RUBY_APPLICATION_START
if exist C:\secret\secretsecret\Xampp\lucene\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\lucene\scripts\ctl.bat START)
if exist C:\secret\secretsecret\Xampp\third_application\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\third_application\scripts\ctl.bat START)
goto end

:stop
echo "Stopping services ..."
if exist C:\secret\secretsecret\Xampp\third_application\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\third_application\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\lucene\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\lucene\scripts\ctl.bat STOP)
rem RUBY_APPLICATION_STOP
if exist C:\secret\secretsecret\Xampp\subversion\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\subversion\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\jetty\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\jetty\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\hypersonic\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\server\hsql-sample-database\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\resin\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\resin\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\apache-tomcat\scripts\ctl.bat (start /MIN /B /WAIT C:\secret\secretsecret\Xampp\apache-tomcat\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\openoffice\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\openoffice\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\apache\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\apache\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\ingres\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\ingres\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\mysql\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\mysql\scripts\ctl.bat STOP)
if exist C:\secret\secretsecret\Xampp\postgresql\scripts\ctl.bat (start /MIN /B C:\secret\secretsecret\Xampp\postgresql\scripts\ctl.bat STOP)

:end

