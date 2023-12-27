# User Management System

This project is a simplified user management system that facilitates the management of users and their group affiliations.

## Technologies Used

- **Backend**: PHP 7.4
- **Database**: MariaDB
- **Frontend**: jQuery with Select2 plugin for enhanced select boxes

## Getting Started

These instructions will get started with the project on your local machine for testing purposes.

### Prerequisites

- Docker
- Docker Compose

### Installing

1. **Clone the Repository**

   ```bash
   git clone https://github.com/Kento1221/user-usergroup-crud-app.git
   cd user-usergroup-crud-app

2. **Run the docker-compose**

    ```bash
     docker-compose up
    ```
3. **Run the migration and seeding**

    ```bash
     composer migrate
     composer seed
    ```
 
  *Before you run the composer commands with your local composer. In the `app\Facades\Database.php`, change `mariadb` host to `localhost:3306`, otherwise the local composer instance won't see the container. After running those command revert to previous value.*

