import React, {Component} from "react";
import PropTypes from "prop-types"
import {Input} from '../../../../../../components'

/**
 * Label for an Anchor
 *
 * @param props
 * @param {Function} props.onBlur
 * @param {Function} props.handleChange
 * @param {string}   props.label
 * @return {XML}
 * @constructor
 */
function AnchorLabel(props) {
    const onBlur       = props.onBlur || null;
    const handleChange = props.handleChange || null;
    const labelText    = props.label || '';
    const isEdit       = props.isEdit || false;
    if (!isEdit) {
        return <span className="schema--anchor--label">
                    {labelText}
                </span>
    }
    
    return <Input autoFocus
                  className="schema--anchor--label"
                  type="text"
                  value={labelText}
    
                  handleChange={handleChange}
                  onBlur={onBlur} />;
}

class Anchor extends Component {
    constructor(props) {
        super(props);
        
        this.state = {
            isEdit: false,
            label:  null
        };
    }
    
    handleClick(event) {
        this.toggleLabelEdit();
    }
    
    handleKeyPress(event) {
        const charCode = event.charCode;
        
        switch (charCode) {
            case 27: //escape key
                
                // this only gets us into edit mode
                if (!this.state.isEdit) return;
                
                this.setState({isEdit: false});
                
                break;
            
            default:
                if (this.state.isEdit) return;
                
                this.toggleLabelEdit();
                
                break;
        }
    }
    
    toggleLabelEdit() {
        this.setState({isEdit: !this.state.isEdit});
    }
    
    _createAnchorLabel() {
        const onBlur       = (value) => {
            this.setState({isEdit: false, label: value || ''})
        };
        const handleChange = (value) => {this.setState({label: value})};
        
        return <AnchorLabel isEdit={this.state.isEdit}
                            label={this.state.label}
                            onBlur={onBlur}
                            handleChange={handleChange} />;
    }
    
    render() {
        const label = this._createAnchorLabel();
        
        return (
            <div className="schema--anchor"
                 data-id={this.props.anchorID}
                 title={this.props.anchorID}
                 onKeyPress={this.handleKeyPress.bind(this)}
                 onClick={this.handleClick.bind(this)}>
                
                <span className="schema--anchor--label--container" tabIndex={0}>
                    {label}
                </span>
            </div>
        );
    }
}

Anchor.propTypes = {
    anchorID: PropTypes.string.isRequired
};

export default Anchor