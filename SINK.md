# Sink Strex

Transactions data from SMS provider Strex

## Data

The data from Strex is transactions/payments done by SMS. It stores
information about code word, phone number, amount, status/result,
operator and the message content sent to the recipient.

## Source

Strex provides SMS payment services. It's a stand-alone service
disconnected from route data and most mechanisms in the public
transport world.  A given set of keywords sent to a given number will
fall within our domain as PTA.

## Usage

It's probably wise to pay attention to the various status columns in
this set as an assurance of whether the transaction went through or
not: `status_code`, `status_code_info`, `result_code`, `result_info`.
