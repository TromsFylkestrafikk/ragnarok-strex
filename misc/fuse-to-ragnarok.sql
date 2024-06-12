-- Copy unavailable strex transactions from fuse to current ragnarok-compatible database.

insert into strex_transactions (
       `transaction_date`,
       `transaction_id`,
       `created`,
       `send_time`,
       `sender`,
       `sender_prefix`,
       `recipient`,
       `recipient_prefix`,
       `message_content`,
       `message_parts`,
       `status_code`,
       `status_code_info`,
       `keyword_id`,
       `keyword`,
       `correlation_id`,
       `session_id`,
       `smsc_transaction_id`,
       `operator`,
       `is_stop_message`,
       `processed`,
       `price`,
       `business_model`,
       `service_code`,
       `merchant_id`,
       `result_code`,
       `result_info`,
       `invoice_text`,
       `age`,
       `is_restricted`,
       `handling_company`,
       `handling_company_info`,
       `tags`,
       `channel_id`,
       `properties`
)
select
       send_date,
       transaction_id,
       concat(send_date, ' ', send_time),
       concat(send_date, ' ', send_time),
       `sender`,
       null,
       `recipient`,
       `recipient_prefix`,
       `content`,
       `message_parts`,
       `status_code`,
       `detailed_status_code`,
       null,
       `keyword`,
       `correlation_id`,
       `session_id`,
       `smsc_transaction_id`,
       `operator_id`,
       0,
       null,
       `price`,
       `business_model`,
       `service_code`,
       `merchant_id`,
       `result_code`,
       `result_description`,
       `invoice_text`,
       `age`,
       case when @is_restricted = 'False' then 0 else 1 end,
       null,
       null,
       tags,
       null,
       null
from fuse.strex_transactions
where send_date < '2022-04-27';
