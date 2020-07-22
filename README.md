# Poem

An **experimental** action based micro api framework which uses a single json endpoint. 

@TODO:
* Add authorization based on user roles
* Format json output via action parameter e.g. "format"

## Single Action Request/Response
``POST http://your.host/api``

```json
{
  "type": "movies",
  "action": "find",
  "payload": {
      "include": "genre"
  }
}
```

### Response (https://jsonapi.org/)

```json
[
    {
        "type": "movies",
        "id": 1,
        "attributes": {
            "name": "Hulk"
        },
        "relationships": {
            "genre": {
                "data": {
                    "id": 1,
                    "type": "genres"
                }
            }
        }
    },
    {
        "type": "movies",
        "id": 2,
        "attributes": {
            "name": "Iron Man"
        },
        "relationships": {
            "genre": {
                "data": {
                    "id": 1,
                    "type": "genres"
                }
            }
        }
    },
]
```
<br />

## Multiple Action Request/Response
``POST http://your.host/api``

```json
[
    {
        "type": "movies",
        "action": "create",
        "payload": {
            "attributes": {
                "name": "Iron Man"
            },
            "format": ["id", "name"]
        },
        
    },

    {
        "type": "movies",
        "action": "create",
        "payload": {
            "attributes": {
                "name": "Hulk"
            },
            "format": ["name"]
        }
    }
]
```

### Formatted Response

```json
[
    {
        "id": 1,
        "name": "Iron Man"
    },

    {
        "name": "Hulk"
    }
]
```