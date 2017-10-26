/**
 * Created by Sam Washington on 10/11/17.
 */

import Sm from "spwashi-sm/src/index";

Sm.entities.Datatype.init('string');
Sm.entities.Datatype.init('null');
Sm.entities.Datatype.init('course_number', {inherits: 'string'});

const models = {
    _:            {
        properties: {
            id: {
                inherits: 'id',
                primary:  !0
            },
        }
    },
    universities: {
        inherits: '_',
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

export default models;