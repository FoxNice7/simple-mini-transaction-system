# PHP PDO User Management & Money Transfer System

## Project Description

This project is a simple PHP application built using **PDO** for database interaction. It demonstrates:

- Logging user actions
- Secure money transfers between users
- Database transactions with rollback support
- Row-level locking using `SELECT ... FOR UPDATE`

The purpose of this project is to practice working with PDO, prepared statements, transactions, and basic database design concepts.

---

## Technologies Used

- PHP (procedural style)
- PDO (PHP Data Objects)
- MySQL
- HTML Forms
- SQL Transactions

---

## Database Structure

### `users` table

| Column      | Type        | Description |
|------------|------------|------------|
| id         | INT (PK)   | User ID |
| name       | VARCHAR    | User name |
| email      | VARCHAR    | User email |
| balance    | DECIMAL    | User account balance |
| created_at | DATETIME   | Creation timestamp |

---

### `logs` table

| Column      | Type        | Description |
|------------|------------|------------|
| id         | INT (PK)   | Log ID |
| action     | VARCHAR    | Action type (`transfer_in`, `transfer_out`) |
| user_id    | INT        | Related user ID |
| created_at | DATETIME   | Log timestamp |
| amount     | DECIMAL    | transfered amount of money|

---

## Features

### Money Transfer

- Transfers funds between users
- Uses database transactions
- Locks rows using `SELECT ... FOR UPDATE`
- Rolls back transaction if:
  - Sender or receiver does not exist
  - Insufficient balance
  - Invalid transfer amount
- Logs:
  - `transfer_out`
  - `transfer_in`

---

## Transaction Logic

Money transfers are executed inside a database transaction:

1. `beginTransaction()`
2. Lock sender and receiver rows (`FOR UPDATE`)
3. Validate balances
4. Update balances
5. Insert logs
6. `commit()` on success
7. `rollBack()` on failure

This guarantees data consistency and prevents race conditions.

---

## Error Handling

- Uses `PDO::ERRMODE_EXCEPTION`
- All database errors throw exceptions
- Transactions are rolled back on failure
- Input validation prevents invalid operations

```sql
CREATE DATABASE testdb;
