<?php
  /* MySQL functions */
  function open_database_connection() {
    include "settings.php";
    $link = mysqli_connect($host, $username, $password, $database);
    
    if (!$link)
      die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    
    mysqli_query($link, "SET NAMES 'UTF8'");
    
    return $link;
  }

  function close_database_connection($link) {
    mysqli_close($link);
  }
    
  /* User Model */
  function get_all_users()
  {
		$link = open_database_connection();
		
		$query = "SELECT * FROM users ORDER BY uid ASC";
		
		$users = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$row['tags'] = get_user_tags($row['uid']);
				$row['equipments'] = get_user_equipments($row['uid']);
				$users[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $users;
  }
  
  function get_all_uids()
  {
		$link = open_database_connection();
		
		$query = "SELECT uid FROM users ORDER BY uid ASC";
		
		$uids = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$uids[] = $row['uid'];
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $uids;
  }
  
  function get_user_by_uid($uid)
  {
		$link = open_database_connection();
		
		$query = "SELECT * FROM users WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
			$user = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $user;
  }
  
  function user_is_admin($uid) {
  		$link = open_database_connection();
  		
  		$query = "SELECT * FROM users WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND admin = 1 LIMIT 1";
  		
  		if ($result = mysqli_query($link, $query))
  			$user = mysqli_fetch_assoc($result);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $user;
  }
  
  function user_is_admin_and_password_match($uid, $password) {
		$link = open_database_connection();
		
		$query = "SELECT * FROM users WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND password = SHA1('" . mysqli_real_escape_string($link, $password) . "') AND admin = 1";
		
		if ($result = mysqli_query($link, $query))
			$user = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $user;
  }
  
  function update_user($values)
  {
		$link = open_database_connection();
		
		if(!isset($values['admin']))
		  $values['admin'] = "off";
		
		$query = "UPDATE users SET firstname = '" . mysqli_real_escape_string($link, $values['firstname']) . "', lastname = '" . mysqli_real_escape_string($link, $values['lastname']) . "', email = '" . mysqli_real_escape_string($link, $values['email']) . "', password = SHA1('" . mysqli_real_escape_string($link, $values['password']) . "'), admin = '" . filter_var($values['admin'], FILTER_VALIDATE_BOOLEAN) . "', locale = '" . mysqli_real_escape_string($link, $values['locale']) . "' WHERE uid = '" . mysqli_real_escape_string($link, $values['uid']) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function add_user($values)
  {
		$link = open_database_connection();
		
		$query = "INSERT INTO users (uid, firstname, lastname, email, password, admin, locale, balance) VALUES ('" . mysqli_real_escape_string($link, $values['uid']) . "', '" . mysqli_real_escape_string($link, $values['firstname']) . "', '" . mysqli_real_escape_string($link, $values['lastname']) . "', '" . mysqli_real_escape_string($link, $values['email']) . "', SHA1('" . mysqli_real_escape_string($link, $values['password']) . "'), '" . filter_var($values['admin'], FILTER_VALIDATE_BOOLEAN) . "', '" . mysqli_real_escape_string($link, $values['locale']) . "', '0,00')";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function delete_user($uid)
  {
		$link = open_database_connection();
		
		$query = "DELETE FROM users WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function get_user_balance_status($uid) {
    $user = get_user_by_uid($uid);
    $balance = $user['balance'];
    
    // status 1 : ok, status 2 : warning
    if(balance < 0)
      return 2;
    else
      return 1;
  }
  
  /* Tag Model */
  function get_all_tags() {
  		$link = open_database_connection();
  		
  		$query = "SELECT * FROM tags ORDER BY owner ASC";
  		
  		$tags = array();
  		if ($result = mysqli_query($link, $query)) {
  			// fetch associative array
  			while ($row = mysqli_fetch_assoc($result))
  				$tags[] = $row;
  				
  			// free result set
  			mysqli_free_result($result);
  		}
  		
  		// close connection
    mysqli_close($link);
    
    return $tags;
  }
  
  function get_user_tags($uid) {
		$link = open_database_connection();
		
		$query = "SELECT uid, type FROM tags WHERE owner = '" . mysqli_real_escape_string($link, $uid) . "'";
		
		$tags = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$tags[] = $row;
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $tags;
  }
  
  function get_tag_icon_html($type) {
  		switch ($type) {
  			case 0:
  				return '<i class="icon-credit-card"></i>';
      case 1:
  				return '<i class="icon-ticket"></i>';
      case 2:
  				return '<i class="icon-barcode"></i>';
      case 3:
  				return '<i class="icon-qrcode"></i>';
    }
  }
  
  function get_tag_type($type) {
  		switch ($type) {
  			case 0:
  				return _('Mifare Classic');
      case 1:
  				return _('Mifare UltraLight');
      case 2:
  				return _('Code-barres');
      case 3:
  				return _('QR code');
    }
  }
  
  function get_tag_types() {
  		return [ _('Mifare Classic'), _('Mifare UltraLight'), _('Code-barres'), _('QR code')];
  }
  
  
  function get_tag_by_uid($uid) {
		$link = open_database_connection();
		
		$query = "SELECT * FROM tags WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
			$tag = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $tag;
  }
  
  function get_tag_owner($uid) {
		$link = open_database_connection();
		
		$query = "SELECT owner FROM tags WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
			$owner = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $owner['owner'];
  }
  
  function update_tag($uid, $values)
  {
  		$link = open_database_connection();
  		
  		$query = "UPDATE tags SET owner = '" . mysqli_real_escape_string($link, $values['owner']) . "', keya = '" . mysqli_real_escape_string($link, $values['keya']) . "', type = '" . intval($values['type']) . "' WHERE uid = '$uid' LIMIT 1";
  		
  		$result = mysqli_query($link, $query);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function add_tag($values)
  {
  		$link = open_database_connection();
  		
  		if(isset($values['type0']))
  			$type = 0;
  		elseif(isset($values['type1']))
  			$type = 1;
  		elseif(isset($values['type2']))
  			$type = 2;
  		elseif(isset($values['type3']))
  			$type = 3;
  		elseif(isset($values['type4']))
  			$type = 4;
  		
  		$query = "INSERT INTO tags (uid, owner, keya, type) VALUES ('" . mysqli_real_escape_string($link, $values['uid']) . "', '" . mysqli_real_escape_string($link, $values['owner']) . "', '" . mysqli_real_escape_string($link, $values['keya']) . "', '$type')";
  		
  		$result = mysqli_query($link, $query);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function delete_tag($uid)
  {
  		$link = open_database_connection();
  		
  		$query = "DELETE FROM tags WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
  		
  		$result = mysqli_query($link, $query);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  /* Reader Model */
  function get_all_readers() {
  		$link = open_database_connection();
  		
  		$query = "SELECT * FROM readers ORDER BY id ASC";
  		
  		$readers = array();
  		if ($result = mysqli_query($link, $query)) {
  			// fetch associative array
  			while ($row = mysqli_fetch_assoc($result)) {
  			  $row['permissions'] = get_reader_permissions($row['id']);
  				$readers[] = $row;
  			}
  				
  			// free result set
  			mysqli_free_result($result);
  		}
  		
  		// close connection
    mysqli_close($link);
    
    return $readers;
  }
  
  function get_all_ids() {
  		$link = open_database_connection();
  		
  		$query = "SELECT id FROM readers ORDER BY id ASC";
  		
  		$ids = array();
  		if ($result = mysqli_query($link, $query)) {
  			// fetch associative array
  			while ($row = mysqli_fetch_assoc($result))
  				$ids[] = $row['id'];
  				
  			// free result set
  			mysqli_free_result($result);
  		}
  		
  		// close connection
    mysqli_close($link);
    
    return $ids;
  }
  
  function get_reader_permissions($id) {
  		$link = open_database_connection();
  		
  		$query = "SELECT uid, end FROM permissions WHERE id = '" . mysqli_real_escape_string($link, $id) . "'";
  		
  		$permissions = array();
  		if ($result = mysqli_query($link, $query)) {
  			// fetch associative array
  			while ($row = mysqli_fetch_assoc($result))
  				$permissions[] = $row;
  				
  			// free result set
  			mysqli_free_result($result);
  		}
  		
  		// close connection
    mysqli_close($link);
    
    return $permissions;
  }
  
  function get_permission($uid, $id) {
  		$link = open_database_connection();
  		
  		$query = "SELECT * FROM permissions WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND id = '" . mysqli_real_escape_string($link, $id) . "'";
  		
  		if ($result = mysqli_query($link, $query))
  			$permission = mysqli_fetch_assoc($result);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $permission;
  }
  
  function get_service_icon_html($service) {
    switch ($service) {
      case 0:
        return '<span class="tooltip" data-tip-text="' .  get_reader_service($service) . '"data-tip-where="up" data-tip-color="black"><span class="icon-stack"><i class="icon-check-empty icon-stack-base"></i><i class="icon-unlock"></i></span></span>';
      case 1:
        return '<span class="tooltip" data-tip-text="' .  get_reader_service($service) . '"data-tip-where="up" data-tip-color="black"><span class="icon-stack"><i class="icon-check-empty icon-stack-base"></i><i class="icon-shopping-cart"></i></span></span>';
  		case 2:
  		  return '<span class="tooltip" data-tip-text="' .  get_reader_service($service) . '"data-tip-where="up" data-tip-color="black"><span class="icon-stack"><i class="icon-check-empty icon-stack-base"></i><i class="icon-tablet"></i></span></span>';
    }
  }
  
  function get_reader_service($service) {
    switch ($service) {
			case 0:
			  return _('Déverouillage de porte');
      case 1:
  				return _('Paiement');
      case 2:
  				return _('Location de matériel');
    }
  }
  
  function get_reader_services() {
  		return [ _('Ouverture de porte'), _('Paiement'), _('Location de matériel')];
  }
  
  
  function get_reader_by_id($id) {
		$link = open_database_connection();
		
		$query = "SELECT * FROM readers WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query)) {
			$reader = mysqli_fetch_assoc($result);
			$reader['permissions'] = get_reader_permissions($reader['id']);
		}
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $reader;
  }
  
  function is_payment_reader($id) {
		$services = get_reader_services_by_id($id);
		if(in_array(1, $services) || $id == 0)
		  return true;
		else
		  return false;
	}
  
  function get_reader_services_by_id($id) {
		$link = open_database_connection();
		
		$query = "SELECT services FROM readers WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
		  $services = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return explode(',', $services['services']);
  }
  
  
  
  function get_reader_location_by_id($id) {
		$link = open_database_connection();
		
		$query = "SELECT location FROM readers WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query)) {
			$reader = mysqli_fetch_assoc($result);
		}
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $reader['location'];
  }
  
  function update_reader($id, $values)
  {
		$link = open_database_connection();
		
		$query = "UPDATE tags SET owner = '" . mysqli_real_escape_string($link, $values['owner']) . "', keya = '" . mysqli_real_escape_string($link, $values['keya']) . "', type = '" . intval($values['type']) . "' WHERE uid = '$uid' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function add_reader($values)
  {
		$link = open_database_connection();
		
		if(isset($values['type0']))
			$type = 0;
		elseif(isset($values['type1']))
			$type = 1;
		elseif(isset($values['type2']))
			$type = 2;
		elseif(isset($values['type3']))
			$type = 3;
		elseif(isset($values['type4']))
			$type = 4;
		
		$query = "INSERT INTO tags (uid, owner, keya, type) VALUES ('" . mysqli_real_escape_string($link, $values['uid']) . "', '" . mysqli_real_escape_string($link, $values['owner']) . "', '" . mysqli_real_escape_string($link, $values['keya']) . "', '$type')";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function delete_reader($id)
  {
		$link = open_database_connection();
		
		$query = "DELETE FROM readers WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function add_all_user_to_a_reader($id)
  {		
		$uids = get_all_uids();
		foreach ($uids as $uid) {
		  add_permission($uid, $id, null);
		}
  }
  
  function add_permission($uid, $id, $end)
  {
    $link = open_database_connection();
    
    if($end)
      $end_str = "'" . mysqli_real_escape_string($link, $end) . "'";
    else
      $end_str = "NULL";
    
    $query = "INSERT INTO permissions (uid, id, end) VALUES ('" . mysqli_real_escape_string($link, $uid) . "', '" . mysqli_real_escape_string($link, $id) . "', $end_str)";
    
    $result = mysqli_query($link, $query);
    
    // free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  
  
  function delete_permission($uid, $id)
  {
		$link = open_database_connection();
		
		$query = "DELETE FROM permissions WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function update_permission($uid, $id, $end)
  {
    $link = open_database_connection();
    
    if($end)
      $end_str = "'" . mysqli_real_escape_string($link, $end) . "'";
    else
      $end_str = "NULL";
		
		$query = "UPDATE permissions SET end = $end_str WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  /* Swipe Model */
  function get_all_swipes()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes ORDER BY timestamp DESC LIMIT 0,200";
    
    $swipes = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
			  $row['location'] = get_reader_location_by_id($row['reader']);
				$swipes[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipes;
  }
  
  function get_all_swipes_by_reader($id)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE reader = '" . mysqli_real_escape_string($link, $id) . "' ORDER BY timestamp DESC";
    
    $swipes = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$swipes[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipes;
  }
  
  
  function get_all_swipes_today()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE DAY(timestamp) = DAY(NOW()) AND MONTH(timestamp) = MONTH(NOW()) AND YEAR(timestamp) = YEAR(NOW())";
    
    $swipes = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$swipes[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipes;
  }
  
  function get_all_swipes_this_month()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE MONTH(timestamp) = MONTH(NOW()) AND YEAR(timestamp) = YEAR(NOW())";
    
    $swipes = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$swipes[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipes;
  }
  
  function get_all_swipes_by_month($month, $year)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE MONTH(timestamp) = '" . mysqli_real_escape_string($link, $month) . "' AND YEAR(timestamp) = '" . mysqli_real_escape_string($link, $year) . "'";
    
    $swipes = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$swipes[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipes;
  }
  
  function get_swipe_by_id($id)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query)) {
			$swipe = mysqli_fetch_assoc($result);
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $swipe;
  }
  
  function add_swipe($id, $uid, $service, $status)
  {
    $link = open_database_connection();
		
		$query = "INSERT INTO swipes (id, timestamp, reader, uid, service, status) VALUES ('', NOW(), '" . mysqli_real_escape_string($link, $id) . "', '" . mysqli_real_escape_string($link, $uid) . "', '" . mysqli_real_escape_string($link, $service) . "', '" . mysqli_real_escape_string($link, $status) . "')";
		
		$result = mysqli_query($link, $query);
		
		$id = mysqli_insert_id($link);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $id;		
  }
  
  /* Order Model */
  function get_all_orders()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM orders ORDER BY id DESC LIMIT 0,200";
    
    $orders = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
			  $swipe = get_swipe_by_id($row['swipe']);
			  $row['timestamp'] = $swipe['timestamp'];
			  $row['uid'] = $swipe['uid'];
			  $row['reader'] = $swipe['reader'];
			  $row['location'] = get_reader_location_by_id($swipe['reader']);
				$orders[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $orders;
  }
  
  function get_last_order_timestamp_by_uid($uid)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' AND service = 1 ORDER BY timestamp DESC LIMIT 0,1";
    
    if ($result = mysqli_query($link, $query)) {
      $swipe = mysqli_fetch_assoc($result);
      $swipe['location'] = get_reader_location_by_id($swipe['reader']);
		}
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $swipe['timestamp'];
  }
  
  function add_order($swipe, $snack, $quantity)
  {
    $link = open_database_connection();
    
    $snack = get_snack_by_id($snack);
    
    if($snack)
      $query = "INSERT INTO orders (id, swipe, snack, quantity) VALUES ('', '" . mysqli_real_escape_string($link, $swipe) . "', '" . mysqli_real_escape_string($link, $snack['id']) . "', '" . mysqli_real_escape_string($link, $quantity) . "')";
  		  
  	$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function get_all_orders_by_uid($uid)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM orders WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' ORDER BY id DESC LIMIT 0,200";
    
    $orders = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$orders[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $orders;
  }
  
  function get_all_orders_by_swipe($swipe)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM orders WHERE swipe = '" . mysqli_real_escape_string($link, $swipe) . "'";
    
    $orders = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$orders[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $orders;
  }
  
  function get_coffees()
  {
    $link = open_database_connection();
    
    $query = "SELECT SUM(quantity) AS coffees FROM orders WHERE snack = 1 OR snack = 2";
    
    if ($result = mysqli_query($link, $query))
			$coffees = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    if(!$coffees['coffees'])
      $coffees['coffees'] = 0;
    
    return $coffees['coffees'];
  }
  
  function get_coffees_this_month()
  {
    $swipes = get_all_swipes_this_month();
     
    $coffees = 0;
    foreach ($swipes as $swipe) {
      $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        if($order['snack'] == 1 || $order['snack'] == 2)
          $coffees+= $order['quantity'];
      }   
    }
    return $coffees;
  }
  
  function get_coffees_by_month($month, $year)
  {
    $swipes = get_all_swipes_by_month($month, $year);
     
    $coffees = 0;
    foreach ($swipes as $swipe) {
      $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        if($order['snack'] == 1 || $order['snack'] == 2)
          $coffees+= $order['quantity'];
      }   
    }
    return $coffees;
  }
  
  function get_coffees_this_month_by_uid($uid)
  {
    $swipes = get_all_swipes_this_month();
     
    $coffees = 0;
    foreach ($swipes as $swipe) {
      if($swipe['uid'] == $uid)
        $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        if($order['snack'] == 1 || $order['snack'] == 2)
          $coffees+= $order['quantity'];
      }   
    }
    return $coffees;
  }
  
  function new_order($values)
  {
    $user = get_user_by_uid($values['client']);
    
    unset($values['client']);
    unset($values['sub']);
    
    $debit = 0.0;
    if($user) {
      $swipe = add_swipe(0, $user['uid'], 1, 1);
      foreach ($values as $snack_id => $quantity)
      {
        if($quantity > 0)
        {
          $snack = get_snack_by_id(intval(str_replace('snack_', '', $snack_id)));
          add_order($swipe, $snack['id'], $quantity);
          $debit += intval($quantity) * floatval($snack['price']);
        }
      }
      if($debit > 0)
        debit_account($user['uid'], $debit);
    }
  }
  
  function get_all_orders_today()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM swipes WHERE DAY(timestamp) = DAY(NOW()) AND MONTH(timestamp) = MONTH(NOW()) AND YEAR(timestamp) = YEAR(NOW())";
    
    $orders = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
			  array_merge($orders, get_all_orders_by_swipe(43));
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $orders;
  }
  
  function get_coffees_today()
  {
    $swipes = get_all_swipes_today();
     
    $coffees = 0;
    foreach ($swipes as $swipe) {
      $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        if($order['snack'] == 1 || $order['snack'] == 2)
          $coffees+= $order['quantity'];
      }   
    }
    return $coffees;
  }
  
  function get_coffees_today_by_uid($uid)
  {
    $swipes = get_all_swipes_today();
     
    $coffees = 0;
    foreach ($swipes as $swipe) {
      if($swipe['uid'] == $uid)
        $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        if($order['snack'] == 1 || $order['snack'] == 2)
          $coffees+= $order['quantity'];
      }   
    }
    return $coffees;
  }
  
  function get_money_spent_today()
  {
    $swipes = get_all_swipes_today();
    
    $orders = array();
    $money_spent_today = 0.0;
    foreach ($swipes as $swipe) {
      $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        $snack = get_snack_by_id($order['snack']);
        $money_spent_today += (intval($order['quantity']) * floatval($snack['price']));   
      }   
    }
    return $money_spent_today;
  }
  
  function get_money_spent_today_by_uid($uid)
  {
    $swipes = get_all_swipes_today();
    
    $orders = array();
    $money_spent_today = 0.0;
    foreach ($swipes as $swipe) {
      if($swipe['uid'] == $uid)
        $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        $snack = get_snack_by_id($order['snack']);
        $money_spent_today += (intval($order['quantity']) * floatval($snack['price']));   
      }   
    }
    return $money_spent_today;
  }
  
  function get_money_spent_this_month()
  {
    $swipes = get_all_swipes_this_month();
    
    $orders = array();
    $money_spent_this_month = 0.0;
    foreach ($swipes as $swipe) {
      $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        $snack = get_snack_by_id($order['snack']);
        $money_spent_this_month += (intval($order['quantity']) * floatval($snack['price']));   
      }   
    }
    return $money_spent_this_month;
  }
  
  function get_money_spent_this_month_by_uid($uid)
  {
    $swipes = get_all_swipes_this_month();
    
    $orders = array();
    $money_spent_this_month = 0.0;
    foreach ($swipes as $swipe) {
      if($swipe['uid'] == $uid)
        $orders = get_all_orders_by_swipe($swipe['id']);
      foreach ($orders as $order) {
        $snack = get_snack_by_id($order['snack']);
        $money_spent_this_month += (intval($order['quantity']) * floatval($snack['price']));   
      }   
    }
    return $money_spent_this_month;
  }
  
  function datetime_to_string($datetime) {
    if(!isset($datetime))
      return _("Aucune date de fin");
  
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    if(date('Y-m-d', strtotime($datetime)) == $today)
      return _("Aujourd'hui à ") . substr($datetime, -8, 5);
    elseif(date('Y-m-d', strtotime($datetime)) == $yesterday)
      return _("Hier à ") . substr($datetime, -8, 5);
    else {
      if(getenv('LANG') == 'fr_FR')
        return date('\L\e d/m/Y \à h:i', strtotime($datetime));
      else
        return $datetime;
    }
  }
  
  function date_to_string($date) {
    if(!isset($date))
      return _("Aucune date de fin");
  
    $today = date('Y-m-d');
    
    if(date('Y-m-d', strtotime($date)) == $today)
      return _("aujourd'hui");
    else {
      if(getenv('LANG') == 'fr_FR')
        return date('d/m/Y', strtotime($date));
      else
        return $date;
    }
  }
  
  /* Message Model */
  function get_all_messages()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM messages ORDER BY timestamp DESC LIMIT 0,200";
    
    $messages = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
			  $user = get_user_by_uid($row['uid']);
			  $row['firstname'] = $user['firstname'];
			  $row['lastname'] = $user['lastname'];
				$messages[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $messages;
  }
  
  function add_message($uid, $message)
  {
    $link = open_database_connection();
    
    $user = get_user_by_uid($uid);
    
    if($user)
      $query = "INSERT INTO messages (id, uid, timestamp, message) VALUES ('', '" . mysqli_real_escape_string($link, $uid) . "', NOW(), '" . mysqli_real_escape_string($link, $message) . "')";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;		
  }
  
  /* Payment Model */
  function get_all_payments()
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM payments ORDER BY timestamp DESC LIMIT 0,200";
    
    $payments = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$user = get_user_by_uid($row['uid']);
				$row['firstname'] = $user['firstname'];
				$row['lastname'] = $user['lastname'];
				$payments[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $payments;
  }
  
  function get_all_payments_by_uid($uid)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM payments WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' ORDER BY timestamp DESC";
    
    $payments = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result)) {
				$payments[] = $row;
			}
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $payments;
  }
  
  function get_last_payment_by_uid($uid)
  {
    $link = open_database_connection();
    
    $query = "SELECT * FROM payments WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' ORDER BY timestamp DESC LIMIT 0,1";
    
    if ($result = mysqli_query($link, $query))
      $payment = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $payment;
  }
  
  function add_payment($uid, $amount)
  {
  		//TODO Store the IP client address, can be useful if you wanna blacklist hackers
  		if(floatval($amount) > 0) {
		  $link = open_database_connection();
		
			$query = "INSERT INTO payments (uid, amount, timestamp) VALUES ('" . mysqli_real_escape_string($link, $uid) . "', '" . floatval($amount) . "', '" . date('Y-m-d H:i:s') . "')";
		
			$result = mysqli_query($link, $query);
		
			// free result set
			mysqli_free_result($result);
		
			// close connection
		  mysqli_close($link);
		  
		  // credit user account
		  credit_account($uid, $amount);
		  
		  return false;
		}
		else
			return true;
  }
  
  function credit_account($uid, $amount)
  {
  		$user = get_user_by_uid($uid);
  		$current_balance = $user['balance'];
  		$new_balance = $current_balance + floatval($amount);
  		
  		$link = open_database_connection();
  		
  		$query = "UPDATE users SET balance = '$new_balance' WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
  		
  		$result = mysqli_query($link, $query);
  		
  		// free result set
  		mysqli_free_result($result);
  		
  		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function debit_account($uid, $amount)
  {
		$user = get_user_by_uid($uid);
		$current_balance = $user['balance'];
		$new_balance = $current_balance - floatval($amount);
		
		$link = open_database_connection();
		
		$query = "UPDATE users SET balance = '$new_balance' WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  /* Snack Model */
  function get_all_snacks() {
    $link = open_database_connection();
		
		$query = "SELECT * FROM snacks";
		
		$snacks = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$snacks[] = $row;
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $snacks;
  }
  
  function get_visible_snacks() {
    $link = open_database_connection();
		
		$query = "SELECT * FROM snacks WHERE visible = 1";
		
		$snacks = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$snacks[] = $row;
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $snacks;
  }
  
  function add_snack($description_fr_FR, $description_en_US, $price, $visible)
  {
  		$link = open_database_connection();
  		
  		$query = "INSERT INTO snacks (id, description_fr_FR, description_en_US, price, visible) VALUES ('', '" . mysqli_real_escape_string($link, $description_fr_FR) . "', '" . mysqli_real_escape_string($link, $description_en_US) . "', '" . floatval($price) . "', '" . filter_var($visible, FILTER_VALIDATE_BOOLEAN) . "')";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function get_snack_by_id($id) {
  		$link = open_database_connection();
		
		$query = "SELECT * FROM snacks WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
			$snack = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $snack;
  }
  
  function delete_snack($id)
  {
		$link = open_database_connection();
		
		$query = "DELETE FROM snacks WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function update_snack($id, $description_fr_FR, $description_en_US, $price, $visible)
  {
    $link = open_database_connection();
    
    if(!isset($visible))
      $visible = "off";
    
    $query = "UPDATE snacks SET description_fr_FR = '" . mysqli_real_escape_string($link, $description_fr_FR) . "', description_en_US = '" . mysqli_real_escape_string($link, $description_en_US) . "', price = '" . mysqli_real_escape_string($link, $price) . "', visible = '" . filter_var($visible, FILTER_VALIDATE_BOOLEAN) . "' WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
  		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  /* Equipment Model */
  function get_all_equipments() {
    $link = open_database_connection();
		
		$query = "SELECT * FROM equipments ORDER BY name ASC";
		
		$equipments = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$equipments[] = $row;
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $equipments;
  }
  
  function get_user_equipments($uid) {
    $link = open_database_connection();
		
		$query = "SELECT * FROM equipments WHERE hirer = '" . mysqli_real_escape_string($link, $uid) . "'  ORDER BY name ASC";
		
		$equipments = array();
		if ($result = mysqli_query($link, $query)) {
			// fetch associative array
			while ($row = mysqli_fetch_assoc($result))
				$equipments[] = $row;
				
			// free result set
			mysqli_free_result($result);
		}
		
		// close connection
    mysqli_close($link);
    
    return $equipments;
  }
  
  function add_equipment($uid, $name, $description, $hirer, $end)
  {
  		$link = open_database_connection();
  		
  		$query = "INSERT INTO equipments (id, uid, name, description, hirer, start, end) VALUES (NULL, '" . mysqli_real_escape_string($link, $uid) . "', '" . mysqli_real_escape_string($link, $name) . "', '" . mysqli_real_escape_string($link, $description) . "', '" . mysqli_real_escape_string($link, $hirer) . "', NOW(), '" . mysqli_real_escape_string($link, $end) . "')";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function get_equipment_by_id($id) {
  		$link = open_database_connection();
		
		$query = "SELECT * FROM equipments WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		if ($result = mysqli_query($link, $query))
			$equipment = mysqli_fetch_assoc($result);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $equipment;
  }
  
  function delete_equipment($id)
  {
		$link = open_database_connection();
		
		$query = "DELETE FROM equipments WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function update_equipment($id, $uid, $name, $description, $hirer, $end)
  {
		$link = open_database_connection();
		
		$query = "UPDATE equipments SET uid = '" . mysqli_real_escape_string($link, $uid) . "', name = '" . mysqli_real_escape_string($link, $name) . "', description = '" . mysqli_real_escape_string($link, $description) . "', hirer = '" . mysqli_real_escape_string($link, $hirer) . "', start = NOW(), end = '" . mysqli_real_escape_string($link, $end) . "' WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function set_equipment_available($id)
  {
		$link = open_database_connection();
		
		$query = "UPDATE equipments SET hirer = NULL, start = NULL, end = NULL WHERE id = '" . mysqli_real_escape_string($link, $id) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function update_user_locale($uid, $locale)
  {
		$link = open_database_connection();
		
		$query = "UPDATE users SET locale = '" . mysqli_real_escape_string($link, $locale) . "' WHERE uid = '" . mysqli_real_escape_string($link, $uid) . "' LIMIT 1";
		
		$result = mysqli_query($link, $query);
		
		// free result set
		mysqli_free_result($result);
		
		// close connection
    mysqli_close($link);
    
    return $result;
  }
  
  function get_ldap_users()
  {
	  //TODO get the fucking LDAP server working first 
	  $users = get_all_users();
	  return $users;
  }
  
  function get_google_calendar_events()
  {
    $xml = json_decode(json_encode((array) simplexml_load_file("calendar.xml")), 1);
    
    return $xml['entry'];
  }
  
  function cmp($a, $b)
  {
    if ($a["balance"] == $b["balance"]) {
        return 0;
    }
    return ($a["balance"] > $b["balance"]) ? -1 : 1;
  }
  
?>
