* execute below commands to enter framework root folder.
  
  cd analytics_app

* rename .env.example to .env

* configure DB in .env file 

* run below command to install dependencies

  composer install

* run below command to run migration and create tables in the DB

  php artisan migrate

* add data in 'analytics' table in the DB to test the code

* run php artisan serve

* 2 separate endpoints are developed to fetch day wise and monthly analytics reports.
    
    http://127.0.0.1:8000/api/analytics/report_daily

    http://127.0.0.1:8000/api/analytics/report_monthly

    input JSON

	{
		"date_from":"2021-02-01",
		"date_to":"2022-03-16",
	    "country":"india",
	    "referer":"facebook"
	}

Note: The archival mechanism is just implemented and the archived data table is not considered for data pull in the developed APIs.

* An archival mechanism implemented to push 6 month elder data to new table 'analytics_archived' and delete from 'analytics' table, which is developed as an artisan command and this command will be executed as a cron job every week(every monday at 00:05 AM). This archival table not considered in above APIs now above endpoints only fetching data from 'analytics' table only.

