<?php

function get_the_rpr_recipe_schema_data() {

	// Get the recipe id
	if ( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] !== '' ) {
		$recipe_id = $GLOBALS['recipe_id'];
	} else {
		$recipe_id = get_post()->ID;
	}
	$recipe = get_post_custom( $recipe_id );
	$instructions = maybe_unserialize( $recipe['rpr_recipe_instructions'][0] );
	$ingredients  = maybe_unserialize( $recipe['rpr_recipe_ingredients'][0] );
	$comments = get_comments( array(
		'post_id' => $recipe_id,
	) );
	$course = '' !== get_the_rpr_taxonomy_terms( 'course', false, false, ', ', true )
		? get_the_rpr_taxonomy_terms( 'course', false, false, ', ', true )
		: 'Main Course';
	$cuisine = '' !== get_the_rpr_taxonomy_terms( 'cuisine', false, false, ', ', true )
		? get_the_rpr_taxonomy_terms( 'cuisine', false, false, ', ', true )
		: 'American';
	$_keyword = get_the_tags( $recipe_id );
	if ( $_keyword ) {
		foreach ( $_keyword as $keyword ) {
			$keywords[] = $keyword->name;
		}
	}
	$keywords = ! empty( $keywords ) ? $keywords : array( 'healthy vegan recipe' );

	$data = array();

	$data['@context']      = 'http://schema.org';
	$data['@type']         = 'Recipe';
	$data['name']          = get_the_title( $recipe_id );

	// Images
	$data['image']         = array(
		get_the_post_thumbnail_url( $recipe_id, 'thumbnail' ),
		get_the_post_thumbnail_url( $recipe_id, 'medium' ),
		get_the_post_thumbnail_url( $recipe_id, 'savor-post-img-small' ),
		get_the_post_thumbnail_url( $recipe_id, 'full' ),
	);

	//Author
	$data['author']        = array(
		'@type'  => 'Person',
		'name'   => get_the_author_meta( 'display_name' ),
		'url'    => get_site_url(),
		'sameAs' => array(
			get_the_author_meta( 'googleplus' ),
			get_the_author_meta( 'twitter' ),
			get_the_author_meta( 'facebook' ),
			get_the_author_meta( 'pinterest' ),
		),
	);

	$data['datePublished'] = get_the_date( 'c', $recipe_id );
	$data['description']   = strip_tags( strip_shortcodes( $recipe['rpr_recipe_description'][0] ) );
	$data['prepTime']      = rpr_format_time_xml( $recipe['rpr_recipe_prep_time'][0] );
	$data['cookTime']      = rpr_format_time_xml( $recipe['rpr_recipe_cook_time'][0] );
	$data['totalTime']     = rpr_format_time_xml( $recipe['rpr_recipe_prep_time'][0]
	                                              + $recipe['rpr_recipe_cook_time'][0]
	                                              +  $recipe['rpr_recipe_passive_time'][0] );
	$data['keywords']       = implode( ', ', $keywords );
	$data['recipeYield']    = esc_attr( $recipe['rpr_recipe_servings'][0] ) . ' ' . esc_attr( $recipe['rpr_recipe_servings_type'][0] );
	$data['recipeCategory'] = $course;
	$data['recipeCuisine']  = $cuisine;

	// Nutrition
	$data['nutrition']  = array(
		'@type'               => 'NutritionInformation',
		'calories'            => esc_attr( $recipe['rpr_recipe_calorific_value'][0] ),
		'carbohydrateContent' => esc_attr( $recipe['rpr_recipe_carbohydrate'][0] ),
		'cholesterolContent'  => esc_attr( $recipe['rpr_recipe_cholesterol'][0] ),
		'fatContent'          => esc_attr( $recipe['rpr_recipe_fat'][0] ),
		'fibreContent'        => esc_attr( $recipe['rpr_recipe_fibre'][0] ),
		'proteinContent'      => esc_attr( $recipe['rpr_recipe_protein'][0] ),
		'saturatedFatContent' => esc_attr( $recipe['rpr_recipe_saturatedFat'][0] ),
		'sodiumContent'       => esc_attr( $recipe['rpr_recipe_sodium'][0] ),
		'sugarContent'        => esc_attr( $recipe['rpr_recipe_sugar'][0] ),
	);

	// Ingredients
	foreach ( (array) $ingredients as $ingredient ) {
		$data['recipeIngredient'][] = $ingredient['amount'] . ' ' . $ingredient['unit']  . ' ' . $ingredient['ingredient'] . ' '. $ingredient['notes'];
	}

	// Instructions
	foreach ( (array) $instructions as $instruction ) {
		$data['recipeInstructions'][] = array(
			'@type' => 'HowToStep',
			'text'  => $instruction['description']
		);
	}

	// Review
	if ( count( $comments ) > 1  ) {
		foreach ( $comments as $comment ) {
			if ( (int) $comment->comment_karma > 0 ) {
				$data['review'][] = array(
					'@type' => 'Review',
					'reviewRating' => array(
						'@type' => 'Rating',
						'ratingValue' => (int) $comment->comment_karma,
						'bestRating'  => 5
					),
					'author' => array(
						'@type' => 'Person',
						'name'  => $comment->comment_author
					),
					'datePublished' => $comment->comment_date_gmt,
					'reviewBody' => $comment->comment_content,
					'publisher' => ''
				);
			}
		}
	}

	// Aggregate rating
	if ( isset( $GLOBALS['c_count'] ) && $GLOBALS['c_count'] >= 1  ) {
		$data['aggregateRating'] = array(
			'@type' => 'AggregateRating',
			'ratingValue' => number_format( $GLOBALS['rating'], 1, '.', ''),
			'ratingCount' => (int) $GLOBALS['c_count']
		);
	}

	// Video
	/*$data['video'][] = array(
		'name' => get_the_title( $recipe_id ),
		'description' => 'This is how you make a Party Coffee Cake.',
		'thumbnailUrl' => array(
			'https://example.com/photos/1x1/photo.jp',
			'https://example.com/photos/4x3/photo.jpg',
			'https://example.com/photos/16x9/photo.jpg'
		),
		'contentUrl' => 'http://www.example.com/video123.flv',
        'embedUrl'   => 'http://www.example.com/videoplayer.swf?video=123',
        'uploadDate' => '2018-02-05T08:00:00+08:00',
	);*/

	// Diet
	$data['suitableForDiet'] = array(
		'http://schema.org/GlutenFreeDiet',
		'http://schema.org/VeganDiet'
	);

	$data = apply_filters( 'rcno_recipe_schema_data_filter', $data );

	return wp_json_encode( $data );
}