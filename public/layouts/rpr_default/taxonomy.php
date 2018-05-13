<?php

/**
 * Render a list of all terms of this taxonomy
 * @todo: create a multicolumn layout
 */

// Create an empty output variable
$out = '';

if ( $terms && ! is_wp_error( $terms ) ) {
	if ( count( $terms ) > 0 ) {
		// Create an index i to compare the number in the list and check for first and last item
		$i = 0;

		// Create an empty array to take with the first letters of all headlines
		$letters = array();

		// Walk through all the terms to build alphabet navigation
		foreach ( (array) $terms as $term ) {
			// Get term meta data
			$term_meta = get_term_meta( $term->term_id );

			// Skip ingredients withe meta 'use_in_list' == false
			if ( ! ( $taxonomy === 'rpr_ingredient' && isset( $term_meta['use_in_list'] ) && $term_meta['use_in_list'] !== 0 ) ) {
				$title = ucfirst( $term->name );

				if ( $headers ) {
					// Add first letter headlines for easier navigation if set so

					// Get the first letter (without special chars)
					$first_letter = substr( normalize_special_chars( $title ), 0, 1 );

					// Check if we've already had a headline
					if ( ! in_array( $first_letter, $letters, true ) ) {
						// Close list of proceeding group:
						if ( $i !== 0 ) {
							$out .= '</div>';
						}
						// Create a headline
						$out .= '<h2><a class="rpr_toplink" href="#top">&uarr;</a><a name="' . $first_letter . '"></a>';
						$out .= strtoupper( $first_letter );
						$out .= '</h2>';

						// Start new list
						$out .= '<div class="rpr_taxlist">';

						// Add the letter to the list
						$letters[] = $first_letter;
					}
				} else {
					// Start list before first item
					if ( $i === 0 ) {
						$out .= '<div class="rpr_taxlist">';
					}
				}

				// Add the entry for the term:
				$out .= '<div class="rpr_taxlist_item">';
				$out .= '<div class="rpr_taxlist_container">';
				$out .= '<a href="' . get_term_link( $term ) . '" title="' . sprintf( 'View %s Recipes', $term->name ) . '">';
				if ( '' !== $term->description ) {
					$out .= '<img src="' . $term->description . '" alt="' . sprintf( 'Recipes in the %s %s', $term->slug, $taxonomy ) . '" />';
				} else {
					$out .= '<img src="http://hsteps.local/wp-content/uploads/2017/09/chickpea-curry2web.jpg" />';
				}
				$out .= '<h5>';
				$out .= $title;
				$out .= '</h5>';
				$out .= '</a>';
				$out .= '</div>';
				$out .= '</div>';

				// increment the counter
				$i ++;
			}
		}
		// Close the last list:
		$out .= '</div>';

		// Output the rendered list
		echo '<a name="top"></a>';
		//the_alphabet_nav_bar( $letters );
		echo $out;
		//the_alphabet_nav_bar( $letters );

	} else {
		// No terms in this taxonomy
		_e( 'There are no terms in this taxonomy.', 'recipepress-reloaded' );
	}
} else {
	// Error: no taxonomy set
	_e( '<b>Error:</b> No taxonomy set for this list!', 'recipepress-reloaded' );
}
