/* global shiroEditorVariables */

import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', {
		content: __( 'Get email updates', 'shiro' ),
		level: 3,
	} ],
	[ 'core/paragraph', { content: __( 'Subscribe to news about ongoing projects and initiatives', 'shiro' ) } ],
];

const iconUrl = shiroEditorVariables.themeUrl + '/assets/dist/icons.svg#email';

export const
	name = 'shiro/mailchimp-subscribe',
	settings = {
		apiVersion: 2,

		icon: 'email',

		title: __( 'Mailchimp subscription form', 'shiro' ),

		attributes: {
			action: {
				type: 'string',
			},
			description: {
				type: 'string',
				default: __( 'This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site’s privacy policy.', 'shiro' ),
			},
			buttonText: {
				type: 'string',
				default: __( 'Subscribe', 'shiro' ),
			},
		},

		/**
		 * Render mailchimp subscribe for the editor
		 */
		edit: function MailChimpSubscribeEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'mailchimp-subscribe' } );
			const { description, buttonText } = attributes;

			return (
				<>
					<div { ...blockProps }>
						<svg className="i icon icon-mail">
							<use xlinkHref={ iconUrl } />
						</svg>
						<InnerBlocks
							template={ BLOCKS_TEMPLATE }
							templateLock={ false } />
						<div className="mailchimp-subscribe__input-container">
							<div className="mailchimp-subscribe__column-input">
								<div
									className="mailchimp-subscribe__input-field"
								>
									{ __( 'Email address', 'shiro' ) }
								</div>
							</div>
							<div className="mailchimp-subscribe__column-button">
								<RichText
									allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
									className="wp-block-shiro-button"
									tagName="div"
									value={ buttonText }
									onChange={ buttonText => setAttributes( { buttonText } ) }
								/>
							</div>
						</div>
						<RichText
							className="has-base-30-color has-text-color has-small-font-size"
							tagName="p"
							value={ description }
							onChange={ description => setAttributes( { description } ) }
						/>
					</div>
				</>
			);
		},

		/**
		 * Render mailchimp subscribe for the frontend
		 */
		save: function MailChimpSubscribeSave( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'mailchimp-subscribe' } );
			const { description, buttonText } = attributes;

			return (
				<div { ...blockProps }>
					<svg className="i icon icon-mail">
						<RawHTML>
							{ '<use xlink:href="' + iconUrl + '" />' }
						</RawHTML>
					</svg>
					<InnerBlocks.Content />
					<div className="mailchimp-subscribe__input-container">
						<div
							className="mailchimp-subscribe__column-input"
						>
							<RawHTML>{ '<!-- input_field -->' }</RawHTML>
						</div>
						<div className="mailchimp-subscribe__column-button">
							<RichText.Content
								className="wp-block-shiro-button"
								tagName="button"
								type="submit"
								value={ buttonText }
							/>
						</div>
					</div>
					<RichText.Content
						className="has-base-30-color has-text-color has-small-font-size"
						tagName="p"
						value={ description }
					/>
				</div>
			);
		},
	};
