# SmPHP
Apologies there isn't a more helpful description of this Framework yet! That is a work-in-progress as well as this framework.

# Configuring the ORM
## Input
This is an example of a JavaScript file that might be used to export the entities the SmJS framework would convert to JSON and save in a file, which this PHP framework would read and configure the Object Relational Mapper with.

```JavaScript
const DATETIME_ = 'datetime';
const STRING_   = 'string';
const INTEGER_  = 'int';
const NULL_     = 'null';

export const models = {
    _:       {
        properties: {
            id:             {
                primary:     !0,
                isGenerated: true,
                datatypes:   INTEGER_,
                length:      11,
            },
            delete_dt:      {
                datatypes: [DATETIME_, NULL_],
            },
            creation_dt:    {
                datatypes:    [DATETIME_],
                defaultValue: 'now'
            },
            last_update_dt: {
                datatypes:   [DATETIME_, NULL_],
                updateValue: 'now'
            },
        }
    },
    users:   {
        inherits: '_',
        
        properties: {
            email:      {length: 255, datatypes: [STRING_], unique: true},
            first_name: {length: 50, datatypes: [STRING_, NULL_]},
            last_name:  {length: 50, datatypes: [STRING_, NULL_]}
        }
    },
    clients: {
        inherits:   '_',
        properties: {
            note:       {datatypes: [STRING_, NULL_], length: 3000,},
            user_id:    {datatypes: INTEGER_, length: 11,},
            clients_id: {datatypes: INTEGER_, length: 11,},
        }
    }
};

export default models;
```
##Output
```JSON
{
   "_": {
      "smID": "[Model]_",
      "propertyMeta": {
         "primary": [
            "[Property]{[Model]_} id"
         ],
         "unique": {}
      },
      "properties": {
         "id": {
            "datatypes": [
               "int"
            ],
            "smID": "[Property]{[Model]_} id",
            "length": 11,
            "primary": true,
            "isGenerated": true
         },
         "delete_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]_} delete_dt"
         },
         "creation_dt": {
            "datatypes": [
               "datetime"
            ],
            "smID": "[Property]{[Model]_} creation_dt",
            "defaultValue": "now"
         },
         "last_update_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]_} last_update_dt",
            "updateValue": "now"
         }
      }
   },
   "users": {
      "smID": "[Model]users",
      "propertyMeta": {
         "primary": [
            "[Property]{[Model]users} id"
         ],
         "unique": {
            "unique_key": [
               "[Property]{[Model]users} email"
            ]
         }
      },
      "properties": {
         "id": {
            "datatypes": [
               "int"
            ],
            "smID": "[Property]{[Model]users} id",
            "length": 11,
            "primary": true,
            "isGenerated": true
         },
         "delete_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]users} delete_dt"
         },
         "creation_dt": {
            "datatypes": [
               "datetime"
            ],
            "smID": "[Property]{[Model]users} creation_dt",
            "defaultValue": "now"
         },
         "last_update_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]users} last_update_dt",
            "updateValue": "now"
         },
         "email": {
            "datatypes": [
               "string"
            ],
            "smID": "[Property]{[Model]users} email",
            "length": 255,
            "unique": true
         },
         "first_name": {
            "datatypes": [
               "string",
               "null"
            ],
            "smID": "[Property]{[Model]users} first_name",
            "length": 50
         },
         "last_name": {
            "datatypes": [
               "string",
               "null"
            ],
            "smID": "[Property]{[Model]users} last_name",
            "length": 50
         }
      }
   },
   "clients": {
      "smID": "[Model]clients",
      "propertyMeta": {
         "primary": [
            "[Property]{[Model]clients} id"
         ],
         "unique": {}
      },
      "properties": {
         "id": {
            "datatypes": [
               "int"
            ],
            "smID": "[Property]{[Model]clients} id",
            "length": 11,
            "primary": true,
            "isGenerated": true
         },
         "delete_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]clients} delete_dt"
         },
         "creation_dt": {
            "datatypes": [
               "datetime"
            ],
            "smID": "[Property]{[Model]clients} creation_dt",
            "defaultValue": "now"
         },
         "last_update_dt": {
            "datatypes": [
               "datetime",
               "null"
            ],
            "smID": "[Property]{[Model]clients} last_update_dt",
            "updateValue": "now"
         },
         "note": {
            "datatypes": [
               "string",
               "null"
            ],
            "smID": "[Property]{[Model]clients} note",
            "length": 3000
         },
         "user_id": {
            "datatypes": [
               "int"
            ],
            "smID": "[Property]{[Model]clients} user_id",
            "length": 11
         },
         "clients_id": {
            "datatypes": [
               "int"
            ],
            "smID": "[Property]{[Model]clients} clients_id",
            "length": 11
         }
      }
   }
}
```
# Used in PHP like
```PHP
$application = $this->app;
        $dataLayer   = $application->data;
        
        
        # Instantiate a Model that we'll use to find a matching object (or throw an error if it doesn't exist)
        $_sam_model                 = $dataLayer->models->instantiate('users');
        $_sam_model->properties->id = 1;
    
        # The Model PersistenceManager is an object that gives us access to standard ORM methods
        ##  find, save, create, mark_delete (haven't implemented DELETE statements yet)
        $modelPersistenceManager = $dataLayer->models->persistenceManager;
        
        
        /** @var Model $sam */
        # This would throw an error if the Model could not be found
        $sam                     = $modelPersistenceManager->find($_sam_model);
    
        if($sam->properties->first_name->value !== 'Samuel'){
            $modelPersistenceManager->mark_delete($sam);
        }else{
            $sam->properties->first_name = 'Mr. Samuel';
            $modelPersistenceManager->save($sam);
        }
    ```
