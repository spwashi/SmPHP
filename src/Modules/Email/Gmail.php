<?php


namespace Sm\Modules\Communication\Email;


use PHPMailer\PHPMailer\PHPMailer;
use Sm\Core\Resolvable\Exception\UnresolvableException;

class Gmail extends Email {
    /** @var \PHPMailer\PHPMailer\PHPMailer */
    protected $instance;
    protected $imgResolutionDir;
    protected $subject;
    protected $reply_to;
    protected $from;
    protected $debug;
    protected $folder = 'Sent Mail';
    
    #
    ##  Constructors/Initialization
    public function __construct($username, $password, $host = 'smtp.gmail.com', $port = 587) {
        parent::__construct($username, $password, $host, $port);
    }
    public static function init($username, $password): Gmail {
        return new static($username, $password);
    }
    public function initialize(array $from, array $reply_to = null): Email {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $this->host;
        $mail->Port       = $this->port;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->username;
        $mail->Password   = $this->password;
        $this->instance   = $mail;
        
        $this->from     = $from;
        $this->reply_to = $reply_to ?? $from;
        
        $mail->setFrom(...$this->from);
        $mail->addReplyTo(...$this->reply_to);
        
        return $this;
    }
    
    #
    ##  Getters and Setters
    public function setDebug(bool $debug = true) {
        $this->debug = $debug;
        return $this;
    }
    
    
    #
    ##  Email Methods
    public function send(array $to): Email {
        $mail = $this->instance;
        
        if (!isset($this->instance)) {
            throw new UnresolvableException("Trying to interact send uninitialized email");
        }
        
        $mail->SMTPDebug = $this->debug ? 2 : 0;
        $mail->addAddress(...$to);
        $mail->msgHTML($this->content,
                       $this->imgResolutionDir);
        
        $mail->Subject = $this->subject;
        $mail->AltBody = $this->plaintext_content;
        try {
            ob_start();
            $mail->send();
            $this->save();
        } catch (\Exception $e) {
            if ($this->debug) {
                $results['success'] = false;
                $results['output']  = explode("\n", ob_get_contents());
                return $results;
            }
        } finally {
            ob_end_clean();
        }
        
        return $this;
    }
    protected function save() {
        if (!isset($this->instance)) {
            throw new UnresolvableException("Trying to interact send uninitialized email");
        }
        
        //You can change 'Sent Mail' to any other folder or tag
        $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/{$this->folder}";
        $mail = $this->instance;
        //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
        $imapStream = \imap_open($path,
                                 $this->username,
                                 $this->password);
        $result     = \imap_append($imapStream,
                                   $path,
                                   $mail->getSentMIMEMessage());
        
        \imap_close($imapStream);
        
        return $result;
    }
}