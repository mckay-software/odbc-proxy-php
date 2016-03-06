# PHP ODBC Proxy

## Background

We need a web interface to the ClearSCADA ODBC database.

## Configuration

In `.env` you should set:

- `DSN_NAME` (name of your DSN)
- `USE_PERSISTENT_ODBC` (true or false, whether to use a persistent ODBC connection)
- `ENVIRONMENT` (either `PRODUCTION` or `DEVELOPMENT`)

These are optional:

- `ROLLBAR_ACCESS_TOKEN` (for logging to Rollbar dashboard)

## Usage

Set up a PHP website. POST an array of SQL queries, encoded in JSON to `/`. Something like:

```
[
	"SELECT * FROM counters WHERE id=5",
	"SELECT FullName, Type FROM CDBObject WHERE id=1 OR id=2"
]
```

The response will be a nested array. The top-level array in the response corresponds to each SQL query. The inner arrays represent the rows returned by each query.

For example:

```
[
	[
		{
			"id": 5,
			"count": 42,
			"label": "Process value"
		}
	],
	[
		{
			"id": 1,
			"FullName": "SCADAFarm",
			"Type": "CDBGroup"
		},
		{
			"id": 2,
			"FullName": "SCADAFarm.E00000",
			"Type": "CDBGroup"
		}
	]
]
```

## Errors

The script will return HTTP 500 if:

- Any SQL query is invalid
- Can't connect to ODBC
- Could not decode JSON
- JSON was not an array of SQL queries

The script will return HTTP 405 if:

- The POST body was empty

## Troubleshooting

Set `ENVIRONMENT` to `DEVELOP` in your `.env` to turn on verbose logging to `error_log`.
