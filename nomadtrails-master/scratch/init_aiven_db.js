const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../.env.local') });

async function initDB() {
  const connection = await mysql.createConnection({
    host: process.env.DB_HOST,
    port: process.env.DB_PORT,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME,
    ssl: { rejectUnauthorized: false }
  });

  console.log('Connected to Aiven MySQL!');

  try {
    const schemaSql = fs.readFileSync(path.join(__dirname, '../backend/schema.sql'), 'utf8');
    
    // Split SQL by semicolon, but be careful with triggers/stored procedures (none here)
    // Also remove CREATE DATABASE and USE lines to stay in defaultdb
    const commands = schemaSql
      .split(';')
      .map(cmd => cmd.trim())
      .filter(cmd => cmd.length > 0 && !cmd.startsWith('CREATE DATABASE') && !cmd.startsWith('USE'));

    console.log(`Executing ${commands.length} commands...`);

    for (const cmd of commands) {
      try {
        await connection.query(cmd);
      } catch (err) {
        console.error(`Error executing: ${cmd.substring(0, 50)}...`);
        console.error(err.message);
      }
    }

    console.log('Database initialization complete!');
  } catch (err) {
    console.error('Initialization failed:', err);
  } finally {
    await connection.end();
  }
}

initDB();
