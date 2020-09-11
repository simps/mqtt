<?php

namespace Simps\MQTT\Types;

const CONNECT = 1; // Client request to connect to Server

const CONNACK = 2; // Connect acknowledgment

const PUBLISH = 3; // Publish message

const PUBACK = 4; // Publish acknowledgment

const PUBREC = 5; // Publish received (assured delivery part 1)

const PUBREL = 6; // Publish release (assured delivery part 2)

const PUBCOMP = 7; // Publish complete (assured delivery part 3)

const SUBSCRIBE = 8; // Client subscribe request

const SUBACK = 9; // Subscribe acknowledgment

const UNSUBSCRIBE = 10; // Unsubscribe request

const UNSUBACK = 11; // Unsubscribe acknowledgment

const PINGREQ = 12; // PING request

const PINGRESP = 13; // PING response

const DISCONNECT = 14; // Client is disconnecting