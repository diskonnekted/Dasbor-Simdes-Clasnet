import mysql from 'mysql2/promise';

let pool: mysql.Pool;

export function getDb() {
  if (!pool) {
    pool = mysql.createPool({
      host: process.env.DB_HOST || '127.0.0.1',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || '',
      database: process.env.DB_NAME || 'sid',
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0,
    });
  }
  return pool;
}
