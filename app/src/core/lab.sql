-- lab.sql — VulnWeb KANONİK şema + seed. Tek doğruluk kaynağı.
-- bootstrap.php ilk açılışta (users yoksa) ve setup.php / bin/reset.php çağırır.
-- SADECE EĞİTİM. Sahte veri. Statements ';' ile ayrılır (basit splitter ile çalıştırılır).

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS guestbook;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS scores;
DROP TABLE IF EXISTS exploit_logs;
DROP TABLE IF EXISTS files;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(64)  NOT NULL UNIQUE,
  password_md5  VARCHAR(32)  NOT NULL,
  role          VARCHAR(16)  NOT NULL DEFAULT 'user',
  first_name    VARCHAR(64),
  last_name     VARCHAR(64),
  avatar        VARCHAR(255),
  failed_logins INT NOT NULL DEFAULT 0,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE guestbook (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(128),
  comment    TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  owner_id   INT NOT NULL,
  item       VARCHAR(128),
  amount     DECIMAL(10,2),
  secret     VARCHAR(255),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE scores (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  challenge_id VARCHAR(64) NOT NULL,
  solved       TINYINT(1) NOT NULL DEFAULT 1,
  flag         VARCHAR(128),
  solved_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_challenge (user_id, challenge_id)
);

CREATE TABLE exploit_logs (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  ts           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip           VARCHAR(64),
  challenge_id VARCHAR(64),
  payload      TEXT,
  detected     TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE files (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  owner_id      INT,
  path          VARCHAR(255),
  original_name VARCHAR(255),
  mime          VARCHAR(128),
  uploaded_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password_md5, role, first_name, last_name, avatar) VALUES
  ('admin',  '5f4dcc3b5aa765d61d8327deb882cf99', 'admin', 'Admin',  'Istrator', 'admin.jpg'),
  ('gordonb','e99a18c428cb38d5f260853678922e03', 'user',  'Gordon', 'Brown',    'gordonb.jpg'),
  ('1337',   '8d3533d75ae2c3966d7e0d4fcc69216b', 'user',  'Hack',   'Me',       '1337.jpg'),
  ('pablo',  '0d107d09f5bbe40cade3de5c71e9e9b7', 'user',  'Pablo',  'Picasso',  'pablo.jpg'),
  ('smithy', '5f4dcc3b5aa765d61d8327deb882cf99', 'user',  'Bob',    'Smith',    'smithy.jpg');

INSERT INTO guestbook (name, comment) VALUES
  ('Test', 'Bu uygulama egitim amaclidir.'),
  ('Egitmen', 'Stored XSS denemek icin yorum birakin.');

INSERT INTO orders (owner_id, item, amount, secret) VALUES
  (1, 'Sunucu lisansi',   1299.00, 'FLAG{idor-admin-order-1}'),
  (2, 'Klavye',             49.90, 'gordon-gizli-not'),
  (3, 'Monitor',           899.00, 'leet-gizli-not'),
  (4, 'Tablo (orijinal)', 50000.00,'pablo-gizli-not'),
  (5, 'Fare',               19.90, 'smithy-gizli-not');
