import React from "react";
import * as actionTypes from "../../actionTypes"
import * as utility from "../../../../utility";

export default (state, action) => {
    switch (action.type) {
        case actionTypes.CREATE_SCHEMA:
            return utility.randomString()
    }
    return {};
}