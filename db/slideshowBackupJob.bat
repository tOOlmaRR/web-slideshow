@echo off
cls
echo Starting backup...
REM ** Build the Current Timestamp **
set yyyy=%date:~0,4%
set mm=%date:~5,2%
set dd=%date:~8,2%
set hh=%time:~0,2%
set ii=%time:~3,2%
set ss=%time:~6,2%
set Timestamp=%yyyy%%mm%%dd%_%hh%%ii%%ss%
echo Timestamp = %Timestamp%

REM Build Filenames: backup sourec file, timestamped backup file, and current backup file
REM Current backup file is overwritten after each backup
set SourceFilename="C:\Program Files\Microsoft SQL Server\MSSQL14.GMARRMSSQL1\MSSQL\Backup\Automated\WebSlideshowDB-auto.bak"
set TargetFilenameCurrent="C:\Program Files\Microsoft SQL Server\MSSQL14.GMARRMSSQL1\MSSQL\Backup\Automated\WebSlideshowDB-%timestamp%.bak"
set TargetFilenameLatest="C:\Program Files\Microsoft SQL Server\MSSQL14.GMARRMSSQL1\MSSQL\Backup\Automated\WebSlideshowDB-latest.bak"
echo SourceFilename = %SourceFilename%
echo TargetFilenameCurrent = %TargetFilenameCurrent%
echo TargetFilenameLatest = %TargetFilenameLatest%

REM Backup the database
sqlcmd -S MARR2\GMARRMSSQL1 -E -Q "BACKUP DATABASE WebSlideshow TO SlideshowAutoBackup"

REM Overwrite the latest backup
copy /Y %SourceFilename% %TargetFilenameLatest%

REM Rename the backup to include a timestamp
copy %SourceFilename% %TargetFilenameCurrent%

REM Delete originalbackup
del %SourceFilename%
