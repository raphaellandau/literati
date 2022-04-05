<?php
/**
 * ET Help Doc Comment
 *
 * @category  Views
 * @package   user-activity-tracking
 * @author    Moove Agency
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$demo_url = 'https://www.YourDomain.com';
?>
<h2><?php esc_html_e( 'Event Triggers Examples', 'user-activity-tracking-and-log' ); ?></h2>
<hr />
<h4>You can use the examples listed below as a guide for how to setup the event triggers.</h4>

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track PDF downloads</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target" selected>Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains" selected>Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="dummy.pdf">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<a' ) . ' target="_blank" href="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/' ); ?><span>dummy.pdf</span><?php echo esc_attr( htmlentities( '>Download a Dummy PDF</a>' ) ); ?></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track clicks using email address</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target" selected>Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains" selected>Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="sales@example.com">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<a' ) . ' href="mailto:' ); ?><span>sales@example.com</span><?php echo esc_attr( htmlentities( '>Contact Us</a>' ) ); ?></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track clicks using URL</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target" selected>Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains">Contains</option>
											<option value="equals" selected>Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="<?php echo esc_url( $demo_url ); ?>/sample-page/">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<a' ) ); ?> href="<span><?php echo esc_url( $demo_url ); ?>/sample-page/</span>">Find Out More<?php echo esc_attr( htmlentities( '</a>' ) ); ?></code></li>	
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track clicks using button labels</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text" selected>Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains">Contains</option>
											<option value="equals" selected>Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="Watch Video">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<a href="https://vimeo.com/305493827" target="_blank">' ) ); ?><span>Watch Video</span><?php echo esc_attr( htmlentities( '</a>' ) ); ?></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track clicks using element ID</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element" selected>Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains">Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector" selected>CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="#cta-watch-video">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<a href="https://vimeo.com/305493827"' ) . ' id=' ); ?>"<span>cta-watch-video</span>" <?php echo esc_attr( htmlentities( 'target="_blank">Watch Video</a>' ) ); ?></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track clicks using CSS class</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url">Page URL</option>
											<option value="click_element" selected>Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains">Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector" selected>CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value=".submit">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_attr( htmlentities( '<button type="submit"' ) . ' class=' ); ?>"<span>submit</span>"<?php echo esc_attr( htmlentities( '>Submit form</button>' ) ); ?></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track a single page views</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url" selected>Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains">Contains</option>
											<option value="equals" selected>Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="<?php echo esc_url( $demo_url ); ?>/sample/">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><span><?php echo esc_url( $demo_url ); ?>/sample/</span></code></li>					
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track archive page views</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url" selected>Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains" selected>Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="/news/">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_url( $demo_url ); ?><span>/news/</span></code></li>
					<li><code><?php echo esc_url( $demo_url ); ?>/parent-page<span>/news/</span></code></li>
					<li><code><?php echo esc_url( $demo_url ); ?><span>/news/</span>category-1/</code></li>
					<li><code><?php echo esc_url( $demo_url ); ?><span>/news/</span>category-2/</code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

<div class="uat-et-trigger-box-actions-f uat-et-help-example-f">
	<div class="et-box-content">
		<h4>Track search pages</h4>
		<div class="trigger-collapse-example">
			<table class="form-table trigger-action-table" style="margin: 0">
				<tbody>
					<tr>
						<td style="padding: 0;">
							<p><strong>Trigger Setup</strong></p>
						</td>
						<td style="padding: 0">
							<table>
								<tr>
									<td style="padding: 0">
										<label>Type</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="page_url" selected>Page URL</option>
											<option value="click_element">Click Element</option>
											<option value="click_target">Click Target</option>
											<option value="click_text">Click Text</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Operator</label>
									</td>
									<td style="padding: 0">
										<select disabled style="width: 100%; max-width: 100%;">
											<option value="contains" selected>Contains</option>
											<option value="equals">Equals</option>
											<option value="css-selector">CSS Selector</option>
										</select>
									</td>
								</tr>

								<tr>
									<td style="padding: 0">
										<label>Value</label>
									</td>
									<td style="padding: 0">
										<input type="text" class="regular-text" disabled value="?s=">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="trigger-sample-fired">
				<hr>
				<ul>
					<li><code><?php echo esc_url( $demo_url ); ?><span>/?s=test</span></code></li>
				</ul>
			</div>
			<!-- .trigger-sample-fired -->
		</div>
		<!-- .trigger-collapse-example -->
	</div>
	<!-- .et-box-content -->
</div>
<!-- .uat-et-help-example -->

