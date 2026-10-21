const EMAIL_PATTERN = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;

export function isValidEmail(email) {
  return typeof email === "string" && EMAIL_PATTERN.test(email.trim());
}

export function isValidCPF(cpf) {
  if (typeof cpf !== "string") {
    return false;
  }

  const digits = cpf.replace(/\D/g, "");

  if (digits.length !== 11 || /^(\d)\1{10}$/.test(digits)) {
    return false;
  }

  const calculateCheckDigit = (base, initialWeight) => {
    const sum = base
      .split("")
      .reduce(
        (total, digit, index) =>
          total + Number(digit) * (initialWeight - index),
        0,
      );
    const remainder = (sum * 10) % 11;
    return remainder === 10 ? 0 : remainder;
  };

  const firstDigit = calculateCheckDigit(digits.slice(0, 9), 10);
  const secondDigit = calculateCheckDigit(digits.slice(0, 10), 11);

  return (
    firstDigit === Number(digits[9]) && secondDigit === Number(digits[10])
  );
}

export function isStrongPassword(password) {
  if (typeof password !== "string" || [...password].length < 8) {
    return false;
  }

  return (
    /\p{Lu}/u.test(password) &&
    /\p{N}/u.test(password) &&
    /[\p{P}\p{S}]/u.test(password)
  );
}
