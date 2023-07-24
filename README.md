# GBP Monetary

UK monetary sytem operations

## Getting Started

Follow these steps to set up the project on your local machine.

### Prerequisites

- PHP 7.4 or later
- Composer
- MySQL (or any other supported database)

### Installation

1. Clone the repository:

```bash
   git clone https://github.com/Edward-Zn/monetary.git
```

2. Change to the project directory:

```bash
   cd your-repo
```

3. Install dependencies using Composer:

```bash
   composer install
```

4. Configure the Database:

Create a new MySQL database for the project.
Copy the .env file and adjust the database connection parameters:

```bash
cp .env .env.local
```
# Update .env.local with your database credentials

5. Generate the database schema and run migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Start the development server:

```bash
symfony serve
```

7. Open your browser and access the application at http://localhost:8000

## API Documentation

### Add Cost to Catalogue Item

**Endpoint**: `/catalogue/{identificationCode}/add/{amount}`
**Method**: POST

Adds the specified amount to the cost of the catalogue item identified by {identificationCode}.

**Parameters**:
- `{identificationCode}`: The identification code of the catalogue item.
- `{amount}`: The amount to be added in the format "XpYsZd" (e.g., "5p17s8d").

**Example**:
```bash
Addition
curl -X POST http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/add/2p6s3d

Subtraction
curl -X POST http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/subtract/1p5s11d
```
**Response**:
Cost added successfully. Value was 8p14s2d || Item Name new cost value is 11p20s5d