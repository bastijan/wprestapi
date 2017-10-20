<?php
//data version

class ModuleQueryHandler extends QueryHandler
{


	protected $queries = array (
		"customSQL"  => array
                (
                "query" => "?",
				"paramType" => "s"
        ),
		"getVendorUsermeta"  => array
                (
                "query" => "SELECT * FROM wp_usermeta 
                        WHERE user_id=? AND meta_key IN ('description',
                    'pv_shop_name',
                    'pv_custom_commission_rate',
                    'pv_shop_description',
                    'pv_seller_info',
                    'billing_company',
                    'billing_address_1',
                    'billing_postcode',
                    'billing_city',
                    'billing_state',
                    'billing_phone',
                    'billing_email',
                    'display_name',
		    		'_steuer_nummer',
		    		'_steuer_id')",
                "paramType" => "i"
                ),
		"getVendorUsermetaById"  => array
                (
                "query" => "SELECT * FROM wp_usermeta 
                        WHERE meta_key IN ('description',
                    'pv_shop_name',
                    'pv_custom_commission_rate',
                    'pv_shop_description',
                    'pv_seller_info',
                    'billing_company',
                    'billing_address_1',
                    'billing_postcode',
                    'billing_city',
                    'billing_state',
                    'billing_phone',
                    'billing_email',
                    'display_name',
                     'wp_capabilities')
                    AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_key='wp_capabilities' AND meta_value like '%vendor%') 
                    ORDER BY user_id",
                "paramType" => ""
                ),
		"getUserByid" => array (
				"query" => "SELECT ID AS id, user_login, user_nicename, user_email, user_url, user_status, display_name FROM wp_users 
				            WHERE id=?",
                "paramType" => "i"
		),		
		"getUsers" => array (
				"query" => "SELECT ID AS id, user_nicename, user_email, user_url, user_status, display_name FROM wp_users",
                "paramType" => ""
		),	
		"getUsersUsermeta"  => array
                (
                "query" => "SELECT * FROM wp_usermeta 
					ORDER BY user_id",
                "paramType" => ""
        ),
		"getUserUsermetaById"  => array
                (
                "query" => "SELECT * FROM wp_usermeta 
                        WHERE user_id=?",
                "paramType" => "i"
        ),	
		"getVendorCustomersByVendorId" => array (
				"query" => "SELECT * from wp_users
							where ID in (
								select meta_value from wp_postmeta
								where meta_key='_customer_user' and post_id in (
									select post_id from wp_postmeta where meta_key='_vendor_id' and meta_value=?
								)
							)",
                "paramType" => "i"
		),		
		"getPost" => array
                (
                "query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name FROM wp_posts
                        WHERE wp_posts.post_status='publish' AND wp_posts.post_name=?",
                "paramType" => "s"
                ),
		
        "getLatestPosts" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category'
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC",
				"paramType" => ""
		),

        "getPages" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name FROM wp_posts WHERE wp_posts.post_status='publish' AND wp_posts.post_type='page' ORDER BY wp_posts.post_date DESC",
				"paramType" => ""
		),
        "getLatestPostsView10" => array
		(
				"query" => "SELECT * FROM najnovije10",
				"paramType" => ""
		),
        "getPosts" => array
		(
				"query" => "SELECT * FROM vesti",
				"paramType" => ""
		),
		//kategorija izdvajamo
    "getFavoritePosts" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_terms.name='izdvajamo'
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC LIMIT 5",
				"paramType" => ""
		),
    "getFavoritePostsView5" => array
		(
				"query" => "SELECT * FROM izdvajamo5",
				"paramType" => ""
		),
		"getFavoriteNewsAndImage" => array
		(
		"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
		JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
		JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
		JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
		WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category'  AND wp_terms.term_id=126
		GROUP BY naslov
		ORDER BY wp_posts.post_date DESC LIMIT 4",
		"paramType" => ""
		),

        "getIzdvajamo" => array
		(
				"query" => "SELECT * FROM izdvajamo",
				"paramType" => ""
		),
		"getPostByID" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.id=? ORDER BY id DESC LIMIT 1",
				"paramType" => "i"
		),
		"getTopCategoryList" => array
		(
				"query" => "SELECT wp_terms.term_id, wp_terms.name FROM wp_terms JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id WHERE wp_term_taxonomy.taxonomy='category' and wp_term_taxonomy.parent=0",
				"paramType" => ""
		),
		"getSumOfPostInCatID" => array
		(
				"query" => "SELECT count(*) AS ukupno, wp_terms.name AS kategorija FROM wp_posts JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_terms.term_id=?",
				"paramType" => "i"
		),

		"getSubCategoryListForParentID" => array
		(
				"query" => "SELECT wp_terms.term_id, wp_terms.slug,wp_terms.name FROM wp_terms JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id WHERE wp_term_taxonomy.taxonomy='category' and wp_term_taxonomy.parent=?",
				"paramType" => "i"
		),
		"getPostsWOutIzdvajamo" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category'  AND wp_terms.term_id!=126
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC",
				"paramType" => ""
		),
		"getPostsByCatID" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija, wp_terms.slug AS css_class FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_terms.term_id=?
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC",
				"paramType" => "i"
		),
		"getCatByName" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija, wp_terms.term_id AS cat_id, wp_terms.slug AS css_class FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_terms.name=?
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC",
				"paramType" => "s"
		),
		"getPostsByCatName" => array
		(
				"query" => "SELECT ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija, wp_terms.slug AS css_class FROM wp_posts
					JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
					JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
					WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_terms.slug=?
					GROUP BY naslov
					ORDER BY wp_posts.post_date DESC",
				"paramType" => "s"
		),
		"getPostsByCatIDwImage" => array
		(
		 "query" => "SELECT  p1.*,  wm2.meta_value
				    FROM
				        wp_posts p1
				    LEFT JOIN
				        wp_postmeta wm1
				        ON (
				            wm1.post_id = p1.id
				            AND wm1.meta_value IS NOT NULL
				            AND wm1.meta_key = '_thumbnail_id'
				        )
				    LEFT JOIN
				        wp_postmeta wm2
				        ON (
				            wm1.meta_value = wm2.post_id
				            AND wm2.meta_key = '_wp_attached_file'
				            AND wm2.meta_value IS NOT NULL
				        )
					LEFT JOIN wp_term_relationships ON p1.id=wp_term_relationships.object_id
					LEFT JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
					LEFT JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
				    WHERE
				        p1.post_status='publish'
				        AND wp_term_taxonomy.taxonomy='category'
				        AND p1.post_type='post'
				        AND wp_terms.term_id=?
				    ORDER BY
				        wp_terms.term_id, p1.post_date DESC",
				"paramType" => "i"
		),
		"getSubCategoryListForParentID" => array
		(
				"query" => "SELECT wp_terms.term_id, wp_terms.slug,wp_terms.name FROM wp_terms JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id WHERE wp_term_taxonomy.taxonomy='category' and wp_term_taxonomy.parent=?",
				"paramType" => "i"
		),
		"getSubMenu" => array
		(
				"query" => "SELECT wp_terms.*, wp_term_relationships.*,wp_term_taxonomy.*, wp_posts.*
				FROM wp_terms
				JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id
				JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id=wp_term_relationships.term_taxonomy_id
				JOIN wp_posts ON wp_term_taxonomy.parent=wp_posts.post_parent
				WHERE wp_term_taxonomy.taxonomy='category'
				AND wp_posts.post_type='nav_menu_item'
				AND wp_posts.post_parent=?
				GROUP BY name
				ORDER BY menu_order",
				"paramType" => "i"
		),
		"getMostPopularPosts" => array
		(
				"query" => "SELECT wp_postmeta.meta_value AS citanja, ID AS id, post_title AS naslov, post_date AS vreme, post_content AS vest, guid AS url, post_name, wp_terms.name AS kategorija FROM wp_posts
				JOIN wp_term_relationships ON wp_posts.id=wp_term_relationships.object_id
				JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
				JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
				JOIN wp_postmeta ON wp_posts.ID=wp_postmeta.post_id
				WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_term_taxonomy.taxonomy='category' AND wp_postmeta.meta_key='post_views_count' AND  wp_posts.post_date >= DATE_SUB(NOW(), INTERVAL 15 DAY)
				GROUP BY naslov
				ORDER BY  CAST(wp_postmeta.meta_value as SIGNED INTEGER) DESC",
				"paramType" => ""
		),
		"getSubCategoryListForParentID" => array
		(
		"query" => "SELECT wp_terms.term_id, wp_terms.slug,wp_terms.name FROM wp_terms JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id WHERE wp_term_taxonomy.taxonomy='category' and wp_term_taxonomy.parent=?",
		"paramType" => "i"
	  ),
		"getTopMenu" => array
		(
		"query" => "SELECT wp_terms.term_id,wp_terms.slug AS css_class,wp_terms.name, wp_posts.menu_order, wp_posts.guid, wp_posts.ID,wp_posts.post_parent, wp_posts.post_name
		FROM wp_terms
		JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id
		JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id=wp_term_relationships.term_taxonomy_id
		JOIN wp_posts ON wp_terms.term_id=wp_posts.post_parent
		WHERE wp_term_taxonomy.taxonomy='category'
		AND wp_posts.post_type='nav_menu_item'
		AND wp_term_taxonomy.parent=0
		GROUP BY name
		ORDER BY menu_order",
		"paramType" => ""
	),
	"getFooterMenu" => array
	(
	"query" => "SELECT wp_terms.term_id,wp_terms.slug AS css_class,wp_terms.name, wp_posts.menu_order, wp_posts.guid, wp_posts.ID,wp_posts.post_parent
	FROM wp_terms
	JOIN wp_term_taxonomy ON wp_terms.term_id=wp_term_taxonomy.term_id
	JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id=wp_term_relationships.term_taxonomy_id
	JOIN wp_posts ON wp_terms.term_id=wp_posts.post_parent
	WHERE wp_term_taxonomy.taxonomy='category'
	AND wp_posts.post_type='nav_menu_item'
	AND wp_term_taxonomy.parent=0
	GROUP BY name
	ORDER BY menu_order",
	"paramType" => ""
),
	"getPostsByCatIDwImage" => array
	(
		"query" => "SELECT  p1.*,  wm2.meta_value
		FROM
		wp_posts p1
		LEFT JOIN
		wp_postmeta wm1
		ON (
			wm1.post_id = p1.id
			AND wm1.meta_value IS NOT NULL
			AND wm1.meta_key = '_thumbnail_id'
		)
		LEFT JOIN
		wp_postmeta wm2
		ON (
			wm1.meta_value = wm2.post_id
			AND wm2.meta_key = '_wp_attached_file'
			AND wm2.meta_value IS NOT NULL
		)
		LEFT JOIN wp_term_relationships ON p1.id=wp_term_relationships.object_id
		LEFT JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id
		LEFT JOIN wp_terms ON wp_term_taxonomy.term_id=wp_terms.term_id
		WHERE
		p1.post_status='publish'
		AND wp_term_taxonomy.taxonomy='category'
		AND p1.post_type='post'
		AND wp_terms.term_id=?
		ORDER BY
		wp_terms.term_id, p1.post_date DESC",
		"paramType" => "i"
	),
	"getRelatedPosts" => array
	(
	"query" => "SELECT ID, post_content AS vest, post_title AS naslov, post_name AS url,
        MATCH(post_title) AGAINST (?) AS Similarity
        FROM wp_posts
        WHERE post_status='publish'
        ORDER BY RAND(), Similarity DESC",
	"paramType" => "s"
	),
	"getRelatedPosts1" => array
	(
	"query" => "SELECT p.ID as post_id, p.post_content AS vest, p.post_name as url, p.post_title as naslov,
	 count(*) as cnt
	from wp_term_relationships tr, wp_term_taxonomy tt, wp_terms,
	(select p.ID, p.post_content, p.post_name, p.post_title, p.post_date, wp_terms.slug as category
	from wp_posts p, wp_term_relationships tr, wp_term_taxonomy tt, wp_terms
	where
	p.ID                != ?                 and
	p.post_type         = 'post'              and
	p.post_status       = 'publish'           and
	p.ID                = tr.object_id        and
	tr.term_taxonomy_id = tt.term_taxonomy_id and
	tt.taxonomy         = 'category'   and
	tt.term_id          = wp_terms.term_id
	group by p.ID
	order by wp_terms.term_id
	) p,
	( select distinct wp_terms.slug
	from wp_term_relationships tr, wp_term_taxonomy tt, wp_terms
	where
	tr.object_id        = ?                and
	tr.term_taxonomy_id = tt.term_taxonomy_id and
	tt.taxonomy in ('post_tag')   and
	tt.term_id          = wp_terms.term_id
	) tg

	where
	p.ID                = tr.object_id        and
	tr.term_taxonomy_id = tt.term_taxonomy_id and
	tt.taxonomy in ('post_tag')   and
	tt.term_id          = wp_terms.term_id    and
	wp_terms.slug       = tg.slug

	group by p.post_title

	order by cnt desc, p.post_date desc",
	"paramType" => "ii"
	)

	);
}


?>
