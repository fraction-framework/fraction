<?php

namespace Fraction\Http\Enum;

enum ResponseStatus: int {
  case Continue = 100;
  case SwitchingProtocols = 101;
  case Processing = 102;
  case EarlyHints = 103;
  case OK = 200;
  case Created = 201;
  case Accepted = 202;
  case NonAuthoritativeInformation = 203;
  case NoContent = 204;
  case ResetContent = 205;
  case PartialContent = 206;
  case MultiStatus = 207;
  case AlreadyReported = 208;
  case IMUsed = 226;
  case MultipleChoices = 300;
  case MovedPermanently = 301;
  case Found = 302;
  case SeeOther = 303;
  case NotModified = 304;
  case UseProxy = 305;
  case SwitchProxy = 306;
  case TemporaryRedirect = 307;
  case PermanentRedirect = 308;
  case BadRequest = 400;
  case Unauthorized = 401;
  case PaymentRequired = 402;
  case Forbidden = 403;
  case NotFound = 404;
  case MethodNotAllowed = 405;
  case NotAcceptable = 406;
  case ProxyAuthenticationRequired = 407;
  case RequestTimeout = 408;
  case Conflict = 409;
  case Gone = 410;
  case LengthRequired = 411;
  case InternalServerError = 500;
}
