# GBP Monetary

UK monetary system operations

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
   cd monetary
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
```

Alternative for PowerShell:
```bash
Invoke-WebRequest -Uri 'http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/add/2p6s3d' -Method Post -UseBasicParsing
```
**Response**:
Cost added successfully. Value was 8p14s2d || Item Name new cost value is 11p20s5d

### Subtract Cost from Catalogue Item

**Endpoint**: `/catalogue/{identificationCode}/subtract/{amount}`
**Method**: POST

Subtracts the specified amount from the cost of the catalogue item identified by {identificationCode}.

**Parameters**:
- `{identificationCode}`: The identification code of the catalogue item.
- `{amount}`: The amount to be subtracted in the format "XpYsZd" (e.g., "5p17s8d").

**Example**:
```bash
Subtraction
curl -X POST http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/subtract/1p4s9d
```

Alternative for PowerShell:
```bash
Invoke-WebRequest -Uri 'http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/subtract/1p4s9d' -Method Post -UseBasicParsing
```

**Response**:
Cost subtracted successfully. Value was: 146p 15s 0d || Item Name new cost value is: 145p 10s 3d

### Multiply Cost of Catalogue Item

**Endpoint**: `/catalogue/{identificationCode}/multiply/{multiplier}`
**Method**: POST

Multiplies the item cost of the catalogue item identified by {identificationCode} by specified multiplier

**Parameters**:
- `{identificationCode}`: The identification code of the catalogue item.
- `{multiplier}`: The multiplier for the item cost value (e.g. 3).

**Example**:
```bash
Multiplication
curl -X POST http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/multiply/3
```

Alternative for PowerShell:
```bash
Invoke-WebRequest -Uri 'http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff/multiply/3' -Method Post -UseBasicParsing
```
**Response**:
Cost multiplied successfully. Value was 145p 10s 3d || Item Name new cost value is 436p 10s 9d

### Read Item from Catalogue

**Endpoint**: `/catalogue/{identificationCode}`
**Method**: GET

Gets the catalogue item identified by {identificationCode}

**Parameters**:
- `{identificationCode}`: The identification code of the catalogue item

**Example**:
```bash
Read Item
curl -X GET http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff
```

Alternative for PowerShell:
```bash
Invoke-WebRequest -Uri 'http://monetary.local/catalogue/a9bf5032-6f50-4fe9-b770-3bafe7448aff' -Method Get -UseBasicParsing
```
**Response**:
{"id":5,"identificationCode":"a9bf5032-6f50-4fe9-b770-3bafe7448aff","name":"Item Name","cost":"163344"}

### Read All Items from Catalogue

**Endpoint**: `api/catalogue`
**Method**: GET

Gets all catalogue items

**Example**:
```bash
Read Catalogue
curl -X GET http://monetary.local/api/catalogue
```

Alternative for PowerShell:
```bash
Invoke-WebRequest -Uri 'http://monetary.local/api/catalogue' -Method Get -UseBasicParsing
```
**Response**:
[{"id":5,"identificationCode":"a9bf5032-6f50-4fe9-b770-3bafe7448aff","name":"Alpha","cost":"163344"},{"id":8,"identificationCode":"75db9c61-ca54-4641-a54d-20375ae61e0b","name":"Testing new item","cost...}]
