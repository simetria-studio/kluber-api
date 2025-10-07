
# Script para processar filas do Laravel com configurações otimizadas para arquivos grandes
# Uso: ./process_queue.sh

echo "Iniciando processamento de filas com configurações otimizadas..."

# Configurações para arquivos grandes
php artisan queue:work database \
    --queue=default \
    --timeout=300 \
    --memory=512 \
    --tries=3 \
    --sleep=3 \
    --rest=1 \
    --verbose

echo "Processamento de filas finalizado."
