# internet-plan-service

## Installation
```
git clone https://github.com/AvigarD611/internet-plan-service.git
cd internet-plan-service
composer install
```
## Configuration
```
Make sure you have already setup everything required to run PHP and MySQL
```
### 1. Copy environment file
```
cp .env.example .env
```
### 2. Configure the .env file with the right variables values
### 3. Run migrations
```
php workers/manually/run_migrations.php
php workers/scheduled/sync_internet_plans.php
```
### 4. How to apply changes over data
Edit ```project --> mock --> internet_plans.json``` file. 
Then hit the sync button from FE or just run the previous sync worker  

## 
### 

