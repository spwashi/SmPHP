import React, {Component} from "react";
import PropTypes from "prop-types";
import {Button} from "../../../../../../../components/index";
import {connect} from "react-redux";
import schematic_depiction from "../../../../../index";

const EvaluationContainer     = ({isActive, evaluations, addEvaluation}) => {
    if (!isActive) return null;
    
    return (
        <div className="container--control--wrapper">
            <div className="container--control schema--attribute-integration-scheme--evaluation-container--control">
                <Button handleClick={addEvaluation} label="Add Evaluation" />
            </div>
            <div className="evaluation-container schema--attribute-integration-scheme--evaluation-container">
                <ul className="container--items schema--attribute-integration-scheme--evaluation-container--items">
                    {(evaluations || []).map((evaluation, index) => <li key={index}>{evaluation}</li>)}
                </ul>
            </div>
        </div>);
};
EvaluationContainer.propTypes = {
    isActive:      PropTypes.bool,
    evaluations:   PropTypes.array,
    addEvaluation: PropTypes.func
};
export default EvaluationContainer;

export const ActiveEvaluationContainer = connect(
    state => {
        const appState = state[schematic_depiction.constants.NAME] || {};
        return {
            schemas: appState.schemas
        }
    },
    dispatch => {
        return {
            onAddEvaluationClick: () => {
                dispatch()
            }
        }
    })(EvaluationContainer);


