import React from 'react'

class SmEvaluation extends React.Component {
    
    constructor(props) {
        super(props);
        this.state = {
            instantial: null,
            essential:  null
        }
    }
    
    render() {
        return (
            <div key={Math.random() + 'a'} className="evaluation">
                <div className="instantial">{this.state.instantial}!!!</div>
                <div className="essential">{this.state.essential}@@@</div>
            </div>
        );
    }
}

export default class SmEssence extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            evaluations: []
        }
    }
    
    handleAddEvaluationClick(event) {
        this.setState(previous => {
            return {
                evaluations: [
                    ...previous.evaluations,
                    <SmEvaluation />
                ]
            }
        });
        event.stopPropagation();
    }
    
    onFinish() {
        this.setState({lastUpdate: new Date});
    }
    
    render() {
        return (
            <div className="essence">
                <button onClick={this.handleAddEvaluationClick.bind(this)} className="addEvaluation">
                    Add Evaluation
                </button>
                {this.state.evaluations}
            </div>
        );
    }
}