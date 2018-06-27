<?php


namespace Sm\Modules\Communication\Email;


use Sm\Communication\CommunicationLayer;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Modules\Communication\Email\Factory\EmailFactory;

class EmailCommunicationModule extends LayerModule {
    /** @var EmailFactory */
    protected $emailFactory;
    public function __construct(EmailFactory $emailFactory) {
        parent::__construct();
        $this->emailFactory = $emailFactory;
    }
    public static function init(EmailFactory $emailFactory): EmailCommunicationModule {
        return new static($emailFactory);
    }
    protected function establishContext(Layer $context = null) {
        if (!($context instanceof CommunicationLayer)) throw new InvalidContextException("Cannot register anything but a CommunicationLayer!");
        parent::establishContext($context);
        /** @var CommunicationLayer $context */
        $context->registerResponseDispatchers([
                                                  Email::class => function (string $subject, $html, $plain_text,
            
                                                                            array $recipients,
            
                                                                            array $from = null,
                                                                            array $reply_to = null) {
                                                      $this->resolveEmailCreator(null)->initialize($from, $reply_to)
                                                           ->setSubject($subject)
                                                           ->setPlaintextContent($html)
                                                           ->setContent($plain_text)
                                                           ->send(...$recipients);
                                                  },
                                              ]);
    }
    
    public function registerEmailCreator($resolver, $name = null) {
        return $this->emailFactory->register($name, $resolver);
    }
    public function resolveEmailCreator(...$args): Email {
        return $this->emailFactory->resolve(...$args);
    }
}