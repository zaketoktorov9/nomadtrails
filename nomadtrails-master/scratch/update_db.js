const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');
require('dotenv').config({ path: '.env.local' });

async function run() {
  const connection = await mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME,
    multipleStatements: true
  });

  const sql = fs.readFileSync(path.join(__dirname, 'backend', 'update_schema.sql'), 'utf8');
  
  try {
    await connection.query(sql);
    console.log('Database updated successfully');
  } catch (err) {
    console.error('Error updating database:', err);
  } finally {
    await connection.end();
  }
}

run();
