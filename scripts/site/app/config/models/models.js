export default Sm => {
    Sm.entities.Datatype.init('string');
    Sm.entities.Datatype.init('null');
    Sm.entities.Datatype.init('course_number', {inherits: 'string'});
    
    const models = {
        _:            {
            properties: {
                id: {primary: !0},
                
                creation_dt:    {},
                last_update_dt: {},
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
    
    return models;
};