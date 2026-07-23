# Railway Procfile
# https://docs.railway.com/reference/procfile

# Web process - Laravel development server
web: php artisan serve --host=0.0.0.0 --port=$PORT

# Worker process - Queue worker for background jobs
worker: php artisan queue:work --sleep=3 --tries=3 --max-time=3600

# Scheduler process - Laravel task scheduler
scheduler: php artisan schedule:work
