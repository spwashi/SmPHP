# SmPHP
Apologies there isn't a more helpful description of this Framework yet! That is a work-in-progress as well as this framework.

# Configuring the ORM
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
