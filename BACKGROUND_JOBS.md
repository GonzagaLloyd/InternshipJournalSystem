# Background Job System for Report Generation

## Overview

The AI report generation feature now runs as a background job instead of blocking the HTTP request. This provides a better user experience, especially for long-running AI operations.

## How It Works

1. **User initiates report generation**: When the user clicks "Weave Fragments", the system creates a job record and dispatches it to the queue.

2. **Background processing**: The job is pushed to the MongoDB queue and processed asynchronously by a worker:
   - Fetches the selected journal entries
   - Calls the Gemini AI API
   - Generates the professional report
   - Updates the job record with the result

3. **Status polling**: The frontend polls the server every 2 seconds to check the job status until it's completed or failed.

4. **Completion**: Once the job completes, the report is displayed to the user.

**Note**: We use the `mongodb` queue driver (provided by `mongodb/laravel-mongodb`) which allows true asynchronous processing with MongoDB. This prevents the UI from freezing during generation.

## Running the Queue Worker

## Running the Queue Worker

Since we are using the `mongodb` queue driver, you **MUST** run a queue worker to process jobs.

```bash
php artisan queue:work --tries=1 --timeout=200
```

**Important**: Keep this terminal open while using the application. If you close it, report generation will stay in "Pending" state forever.

### Option 2: Supervisor (Production)

For production with Redis, use Supervisor to keep the queue worker running.

## Database Tables

### `report_generation_jobs`
Tracks the status of each report generation job:
- `id`: Job ID
- `user_id`: User who requested the report
- `entry_ids`: JSON array of journal entry IDs
- `status`: `pending`, `processing`, `completed`, or `failed`
- `report`: Generated report content (when completed)
- `period`: Date range of the report
- `error_message`: Error details (if failed)
- `completed_at`: Timestamp when job finished

### `jobs`
Laravel's built-in queue table that stores pending jobs.

## API Endpoints

### POST `/reports/generate`
Initiates a background report generation job.

**Request:**
```json
{
  "entry_ids": ["entry_id_1", "entry_id_2", ...]
}
```

**Response:**
```json
{
  "job_id": 123,
  "status": "pending",
  "message": "Report generation started in background"
}
```

### GET `/reports/job/{jobId}`
Checks the status of a report generation job.

**Response (Pending/Processing):**
```json
{
  "job_id": 123,
  "status": "processing"
}
```

**Response (Completed):**
```json
{
  "job_id": 123,
  "status": "completed",
  "report": "# Weekly Progress Report\n\n...",
  "period": {
    "start": "Feb 01, 2026",
    "end": "Feb 07, 2026"
  }
}
```

**Response (Failed):**
```json
{
  "job_id": 123,
  "status": "failed",
  "error": "Error message here"
}
```

## Troubleshooting

### Jobs not processing
- **Check queue worker**: Ensure `php artisan queue:work` is running
- **Check logs**: Look at `storage/logs/laravel.log` for errors
- **Verify job table**: Check `jobs` collection in MongoDB

### Jobs failing
- **Check Gemini API key**: Verify `GEMINI_API_KEY` is set in `.env`
- **Check timeout**: Long reports may need increased timeout (currently 180 seconds)
- **Check logs**: Review `storage/logs/laravel.log` for specific error messages

### Frontend keeps polling forever
- **Check job status in database**: Query `report_generation_jobs` table to see actual status
- **Clear browser cache**: Sometimes old JavaScript is cached
- **Check network tab**: Verify polling requests are succeeding

## Configuration

### Job Timeout
Default: 180 seconds (3 minutes)

To change, edit `app/Jobs/GenerateReportJob.php`:
```php
public $timeout = 300; // 5 minutes
```

### Polling Interval
Default: 2 seconds

To change, edit `resources/js/Components/Reports/ReportGenerator.vue`:
```javascript
pollInterval.value = setInterval(() => {
    pollJobStatus(currentJobId.value);
}, 3000); // 3 seconds
```

## Benefits of Background Jobs

1. **Non-blocking**: Users can continue using the app while report generates
2. **Better error handling**: Failed jobs are tracked and can be retried
3. **Scalability**: Multiple workers can process jobs in parallel
4. **Reliability**: Jobs survive server restarts (stored in database)
5. **User experience**: No timeout errors for long-running operations
