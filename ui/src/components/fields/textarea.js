/**
 * @class TextArea
 * @package ModularContent\Fields
 *
 * A textarea. Set the argument 'richtext' to TRUE to use
 * a WordPress visual editor.
 */

import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';

import RichtextEditor from '../shared/richtext-editor';
import * as RichtextEvents from '../../util/dom/tinymce';
import autobind from 'autobind-decorator';
import classNames from 'classnames';

import styles from './textarea.pcss';

class TextArea extends Component {
	/**
	 * @param {props} props
	 * @constructs TextArea
	 */

	constructor(props) {
		super(props);
		this.fid = _.uniqueId('textarea-field-');
		this.editor = null;
		this.state = {
			text: this.props.data.length ? this.props.data : this.props.default,
		};
	}

	componentDidMount() {
		if (!this.props.richtext) {
			return;
		}

		this.cacheDom();
		this.initTinyMCE();
	}

	componentWillUnmount() {
		this.cleanUp();
	}

	/**
	 * Depending on prop "richtext" this will return the jsx for a simple textarea or a dom ready to spin up a tinymce instance.
	 *
	 * @method getTemplate
	 */

	getTemplate() {
		let Editor;
		if (!this.props.richtext) {
			Editor = (
				<textarea
					className={styles.rawTextarea}
					id={this.fid}
					ref={this.fid}
					name={this.props.name}
					value={this.state.text}
					onChange={this.handleChange}
				/>
			);
		} else {
			Editor = (
				<div
					id={`wp-${this.fid}-wrap`}
					ref={this.fid}
					className="wp-core-ui wp-editor-wrap tmce-active"
				>
					<RichtextEditor
						data={this.props.data}
						fid={this.fid}
						name={this.props.name}
						buttons={this.props.media_buttons}
					/>
				</div>
			);
		}

		return Editor;
	}

	@autobind
	handleChange(data) {
		const text = !this.props.richtext ? data.currentTarget.value : data;

		this.setState({ text });
		this.props.updatePanelData({
			index: this.props.panelIndex,
			name: this.props.name,
			value: text,
		});
	}

	/**
	 * Kick off the TinyMCE if called
	 *
	 * @method initTinyMCE
	 */

	initTinyMCE() {
		if (!this.props.richtext) {
			return;
		}

		RichtextEvents.init({
			editor: this.editor,
			fid: this.fid,
			editor_settings: this.props.editor_settings_reference,
		}, this.handleChange);
	}

	/**
	 * Cache the dom elements this component works on.
	 *
	 * @method cacheDom
	 */

	cacheDom() {
		this.editor = ReactDOM.findDOMNode(this.refs[this.fid]);
	}

	/**
	 * Remove events and destroy tinymce instance and settings on component unmount.
	 *
	 * @method cleanUp
	 */

	cleanUp() {
		if (!this.props.richtext) {
			return;
		}

		RichtextEvents.destroy({
			editor: this.editor,
			fid: this.fid,
		});
	}

	/**
	 * Inject to dom.
	 *
	 * @method render
	 */

	render() {
		const Editor = this.getTemplate();
		const labelClasses = classNames({
			[styles.label]: true,
			'panel-field-label': true,
		});
		const descriptionClasses = classNames({
			[styles.description]: true,
			'panel-field-description': true,
		});
		const fieldClasses = classNames({
			[styles.field]: true,
			'panel-field': true,
		});
		return (
			<div className={fieldClasses}>
				<label className={labelClasses}>{this.props.label}</label>
				{Editor}
				<p className={descriptionClasses}>{this.props.description}</p>
			</div>
		);
	}
}

TextArea.propTypes = {
	data: PropTypes.string,
	panelIndex: PropTypes.number,
	label: PropTypes.string,
	name: PropTypes.string,
	description: PropTypes.string,
	strings: PropTypes.array,
	default: PropTypes.string,
	richtext: PropTypes.bool,
	media_buttons: PropTypes.bool,
	editor_settings_reference: PropTypes.string,
	updatePanelData: PropTypes.func,
};

TextArea.defaultProps = {
	data: '',
	panelIndex: 0,
	label: '',
	name: '',
	description: '',
	strings: [],
	default: '',
	richtext: false,
	media_buttons: true,
	editor_settings_reference: 'content',
	updatePanelData: () => {},
};

export default TextArea;
