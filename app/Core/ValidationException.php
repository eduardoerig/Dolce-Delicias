<?php
declare(strict_types=1);
namespace App\Core;
final class ValidationException extends HttpException {
 public function __construct(public readonly array $errors) { parent::__construct(422, 'Revise os campos indicados.'); }
}
