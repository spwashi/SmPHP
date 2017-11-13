import React, {Component} from "react";
import Evaluation from "./Evaluation";
import EvaluationContainer from "./Evaluation/EvaluationContainer";

let key          = 0;
const getEvalKey = () => {return key++;};

export default class IntegrationScheme extends Component {
    constructor(props) {
        super(props);
        this.state = {evaluations: [], isActive: false};
    }
    
    createEvaluation(): Evaluation {
        return <Evaluation key={getEvalKey()} />
    }
    
    handleLabelClick() {
        this.toggleActive();
        
    }
    
    addEvaluation(evaluation: Evaluation) {
        this.setState((previous, props) => {
            const previousEvaluations = previous.evaluations;
            return {evaluations: [...previousEvaluations, evaluation]}
        });
    }
    
    toggleActive() {
        this.setState({isActive: !this.state.isActive})
    }
    
    handleLabelKeyPress(event) {
        switch (event.charCode) {
            case 32: //space
            case 13: //enter
                this.toggleActive();
                break;
        }
    }
    
    render() {
        const addEvaluation = () => {
            this.addEvaluation(this.createEvaluation());
            console.log(this);
        };
        
        let className = `schema--attribute integration-scheme schema--attribute-integration-scheme ${this.state.isActive ? 'active' : true}`;
        
        return (
            <div className={className}>
                <div className="schema--attribute--label"
                     tabIndex={0}
                     onKeyPress={this.handleLabelKeyPress.bind(this)}
                     onClick={this.handleLabelClick.bind(this)}>#
                </div>
                <EvaluationContainer isActive={this.state.isActive}
                                     addEvaluation={addEvaluation}
                                     evaluations={this.state.evaluations} />
            </div>
        );
    }
};