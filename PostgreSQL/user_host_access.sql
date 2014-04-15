DROP VIEW IF EXISTS v_user_host_access;
DROP FUNCTION IF EXISTS fn_user_host_access();
CREATE OR REPLACE FUNCTION fn_user_host_access() 
RETURNS TABLE(user_id integer, host_id integer, access integer) AS $$
DECLARE
  user_site_id integer := 0;
  user_customer_id integer := 0;
  user_partner_id integer := 0;
  user_carrier_id integer := 0;

  host_site_id integer := 0;
  host_customer_id integer := 0;
  host_partner_id integer := 0;
  host_carrier_id integer := 0;
BEGIN
FOR user_id, user_site_id , user_customer_id, user_partner_id, user_carrier_id IN (SELECT u.user_id, u.site_id, u.customer_id, u.partner_id, u.carrier_id FROM v_users_access u) LOOP
	FOR host_id, host_site_id , host_customer_id, host_partner_id, host_carrier_id IN (SELECT h.host_id, h.site_id, h.customer_id, h.partner_id, h.carrier_id FROM v_host_access h) LOOP
		IF user_carrier_id = 0 THEN
			access := 1;
			RETURN NEXT;
		ELSIF user_carrier_id = host_carrier_id THEN
			IF user_partner_id = 0 THEN
				access := 1;
			RETURN NEXT;
			ELSIF user_partner_id = host_partner_id THEN
				IF user_customer_id = 0 THEN
					access := 1;
					RETURN NEXT;
				ELSIF user_customer_id = host_customer_id THEN
					IF user_site_id = 0 THEN
						access := 1;
					RETURN NEXT;
					ELSIF user_site_id = host_site_id THEN
						access := 1;
					ELSE
						access := 0;
						RETURN NEXT;
					END IF;

						ELSE
							access := 0;
							RETURN NEXT;
						END IF;
					ELSE
				access := 0;
				RETURN NEXT;
			END IF;
		ELSE
			access := 0;
			RETURN NEXT;
		END IF;
	END LOOP;
END LOOP;
    RETURN;
END
$$ LANGUAGE plpgsql;
CREATE OR REPLACE VIEW v_user_host_access AS SELECT user_id, host_id FROM fn_user_host_access() WHERE access = 1 GROUP BY host_id, user_id ORDER BY user_id, host_id;
