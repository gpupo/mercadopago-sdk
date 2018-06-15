# Mercadopago-SDK

SDK Não Oficial para integração a partir de aplicações PHP com as APIs Mercadopago


[![Paypal Donations](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EK6F2WRKG7GNN&item_name=mercadopago-sdk)


## Requisitos para uso

* PHP >= *7.2*
* [curl extension](http://php.net/manual/en/intro.curl.php)
* [Composer Dependency Manager](http://getcomposer.org)

Este componente **não é uma aplicação Stand Alone** e seu objetivo é ser utilizado como biblioteca.
Sua implantação deve ser feita por desenvolvedores experientes.

**Isto não é um Plugin!**

As opções que funcionam no modo de comando apenas servem para depuração em modo de
desenvolvimento.

A documentação mais importante está nos testes unitários. Se você não consegue ler os testes unitários, eu recomendo que não utilize esta biblioteca.



## Direitos autorais e de licença

Este componente está sob a [licença MIT](https://github.com/gpupo/common-sdk/blob/master/LICENSE)

Para a informação dos direitos autorais e de licença você deve ler o arquivo
de [licença](https://github.com/gpupo/common-sdk/blob/master/LICENSE) que é distribuído com este código-fonte.

### Resumo da licença

Exigido:

- Aviso de licença e direitos autorais

Permitido:

- Uso comercial
- Modificação
- Distribuição
- Sublicenciamento

Proibido:

- Responsabilidade Assegurada



---

## Indicadores de qualidade

[![Build Status](https://secure.travis-ci.org/gpupo/mercadopago-sdk.png?branch=master)](http://travis-ci.org/gpupo/mercadopago-sdk)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gpupo/mercadopago-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gpupo/mercadopago-sdk/?branch=master)
[![Code Climate](https://codeclimate.com/github/gpupo/mercadopago-sdk/badges/gpa.svg)](https://codeclimate.com/github/gpupo/mercadopago-sdk)
[![Test Coverage](https://codeclimate.com/github/gpupo/mercadopago-sdk/badges/coverage.svg)](https://codeclimate.com/github/gpupo/mercadopago-sdk/coverage)

---

## Agradecimentos

* A todos os que [contribuiram com patchs](https://github.com/gpupo/mercadopago-sdk/contributors);
* Aos que [fizeram sugestões importantes](https://github.com/gpupo/mercadopago-sdk/issues);
* Aos desenvolvedores que criaram as [bibliotecas utilizadas neste componente](https://github.com/gpupo/mercadopago-sdk/blob/master/Resources/doc/libraries-list.md).

 _- [Gilmar Pupo](https://opensource.gpupo.com/)_


---

## Instalação

Adicione o pacote ``mercadopago-sdk`` ao seu projeto utilizando [composer](http://getcomposer.org):

    composer require gpupo/mercadopago-sdk

---

## Development (with docker)

    docker run -d --name mercadopago-sdk-pod -v "$PWD":/var/www/app gpupo/container-orchestration:php-dev-v1.4.2

    docker exec -it  mercadopago-sdk-pod  /bin/bash

    vendor/bin/phpunit;

    docker stop mercadopago-sdk-pod;


## Links

* [Mercadopago-sdk Composer Package](https://packagist.org/packages/gpupo/mercadopago-sdk) no packagist.org
* [Github Repository](https://github.com/gpupo/mercadopago-sdk/);
* [Documentação oficial](https://www.mercadopago.com.br/developers/pt/plugins_sdks/sdks/official/php/);
