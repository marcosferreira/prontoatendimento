#!/bin/bash

# Configurações do banco
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_USER=""
DB_PASS=""
DB_NAME=""

# Pasta de destino (ajuste conforme necessário)
BACKUP_DIR="writable/backups"
mkdir -p "$BACKUP_DIR"

# Nome do arquivo de backup
DATA=$(date +"%Y-%m-%d_%H-%M-%S")
ARQUIVO="$BACKUP_DIR/backup_completo_${DATA}.sql"

# Comando de backup
mysqldump --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" --port="$DB_PORT" --routines --triggers --single-transaction --quick --lock-tables=false "$DB_NAME" > "$ARQUIVO"

# Verifica se o backup foi criado
if [ $? -eq 0 ] && [ -s "$ARQUIVO" ]; then
  echo "Backup realizado com sucesso: $ARQUIVO"
else
  echo "Erro ao realizar backup!"
  exit 1
fi