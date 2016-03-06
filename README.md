# PHP ODBC Proxy

## Background

We need a web interface to the ClearSCADA ODBC database.

## Configuration

In `.env` you should set:

- `DSN_NAME` (name of your DSN)
- `ENVIRONMENT` (either `PRODUCTION` or `DEVELOPMENT`)

These are optional:

- `ROLLBAR_ACCESS_TOKEN`

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

If any SQL query is invalid, the response will be an HTTP 500.
