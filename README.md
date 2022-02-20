## Expense sharing application

Technologies used : Laravel & MySql

How to install & run

 - Clone repo
 - Install Db - (File name is Db.sql)
 - Update .env
 - Open Postman collection file (Postman.json)
 

Application Flow : 

I created 

 - users table for storing users info 
 - bills table for storing bills info ( Hotel bill ,  flipkart etc)
 - user_expenses table - to keep history of how much involved users have to pay for each transaction
 - balance_records table - to update balance against each user

Flow - >

 User sends Expense type , bill info & how much each one has to pay to api.
 First we will validate data for three expense types.
 After this we will generate a new bill.
 Then for each this we will store how much each user has to pay.
 We also update balances for each user.


Note : I am using laragon as my local setup, I have a virtual hot with url - https://expense-sharing-application.local.
I ignored timestamps and  did everything in the controller itself due to timing constraint.


