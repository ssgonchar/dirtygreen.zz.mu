select id, guid, stockholder_id, owner_id, status_id, record_at from steelitems_history where steelitem_id = 18211 order by id;
select id, guid, stockholder_id, owner_id, status_id, modified_at from steelitems where id = 18211 order by id;
select * from steelitem_timeline where steelitem_id = 18211 order by id;

select * from companies where id in (12712, 12838, 7117, 11980, 16449, 5998) or title like '%ossil%';