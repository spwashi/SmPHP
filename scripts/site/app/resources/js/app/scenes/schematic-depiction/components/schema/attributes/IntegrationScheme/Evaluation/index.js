import React, {Component} from "react";
import EvaluationScheme from "./EvaluationScheme";
import Ampersand from "../../Anchor/Ampersand";
import Anchor from "../../Anchor";
import PropTypes from "prop-types";

class EvaluationArgument extends Component {
    constructor(props) {
        super(props);
        this.ampersand = this.getAmpersand();
        this.state     = {
            argument: props.argument,
        };
    }
    
    render() {
        const essential = this.props.type;
        let className   = `evaluation--argument argument-${essential} schema--attribute-integration-scheme--evaluation--argument-${essential}`;
        
        return (
            <div className={className}>
                {this.state.argument || this.ampersand}
            </div>
        );
    }
    
    getAmpersand() {
        return <Ampersand owner={this} />
    }
}

export default class Evaluation extends Component {
    constructor(props) {
        super(props);
        
        this.state = {
            instantialArgument: props.instantialArgument || null,
            essentialArgument:  props.essentialArgument || null
        };
    }
    
    render() {
        return (
            <div className="evaluation schema--attribute-integration-scheme--evaluation">
                <EvaluationArgument argument={this.state.instantialArgument} type="instantial" />
                <EvaluationArgument argument={this.state.essentialArgument} type="essential" />
            </div>
        );
    }
}
Evaluation.propTypes = {
    instantialArgument: PropTypes.instanceOf(Anchor),
    essentialArgument:  PropTypes.instanceOf(Anchor)
};
export {EvaluationScheme};