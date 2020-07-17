## Poem

An **experimental** action based micro api framework which uses a single json endpoint. 

@TODO:
* Add authorization based on user roles
* Format json output via action parameter e.g. "format"

## Example Request
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

## Example Response (https://jsonapi.org/)

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