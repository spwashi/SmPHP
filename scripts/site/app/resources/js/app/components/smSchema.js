import React from 'react'
import SmEssence from "./smEssence";
import {Title} from "./title";

const schemaTypes = [
    {
        title:       'Perspective',
        description: 'How [item] views things'
    },
    {
        title:       'Concept',
        description: '[item]'
    },
    {
        title:       'Instance',
        description: 'An example/occurrence of [item]'
    },
    {
        title:       'Essence',
        description: 'What [item] is like'
    },
];

export class SmSchema extends React.Component {
    constructor(props) {
        super(props);
        props.identity && SmSchema.items.set(props.identity, this);
        this.state = {
            editing:    false,
            lastUpdate: new Date,
            name:       null,
            type:       schemaTypes[0]
        }
    }
    
    onFinish() {
        this.setState({lastUpdate: new Date});
    }
    
    render() {
        const updateTitle      = (value) => {
            console.log(value);
            this.setState({name: value})
        };
        const options          = [];
        const types            = new Set(schemaTypes);
        let val                = 0;
        const handleTypeChange = (event) => {
            console.log(event.target.value);
            this.setState({type: schemaTypes[event.target.value]})
        };
        
        types.forEach((type, index) => {
            if (this.state.type.title === type.title) {
                val = index;
            }
            options.push(<option key={type.title} value={index}>{type.title}</option>)
        });
        let schemaDropdown = <select onChange={handleTypeChange} value={val}>{options}</select>;
        const classes      = ('type--__' + this.state.type).toLowerCase();
        return (
            <li className={classes} key={this.props.identity} id={this.props.identity} onClick={e => e.stopPropagation()}>
                <Title name={this.state.name} update={updateTitle} />
                {schemaDropdown}
                
                <div className="type--description">{this.state.type.description}</div>
                <SmEssence identity={this.props.identity} />
            </li>
        );
    }
}

SmSchema.items = new Map;
console.log(SmSchema.items);