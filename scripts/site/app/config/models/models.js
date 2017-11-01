export default Sm => {
    const dataPromises = [
        Sm.entities.Datatype.init('string'),
        Sm.entities.Datatype.init('null'),
        Sm.entities.Datatype.init('int'),
        Sm.entities.Datatype.init('datetime'),
        Sm.entities.Datatype.init('bool'),
    ];
    const models       = {
        _:            {
            properties: {
                id:             {primary: !0},
                delete_dt:      {
                    datatypes: 'datetime',
                },
                creation_dt:    {
                    datatypes: 'datetime',
                    _default:  'now'
                },
                last_update_dt: {
                    datatypes: 'datetime'
                },
            }
        },
        universities: {
            inherits: '_',
            
            properties: {
                id: {primary: !0},
            }
        },
        courses:      {
            inherits:   '_',
            properties: {
                department:    {},
                title:         {
                    datatypes: 'string',
                    length:    25,
                    unique:    true
                },
                course_number: {
                    unique:    true,
                    datatypes: ['int', 'null'],
                    length:    11
                }
            }
        }
    };
    
    return Promise.all(dataPromises)
                  .then(result => models);
};