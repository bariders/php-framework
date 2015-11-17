<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class Mail {
    //Header
    protected $_from;
    protected $_to;
    protected $_replyTo;
    protected $_cc;
    protected $_bcc;
    protected $_subject;

    //Content
    protected $_text;
    protected $_files = [];
    protected $_tmplVars = [];


    public function setFrom($address, $name = '')
    {
        $this->_from = $this->_makeAddress($address, $name);
    }

    public function setTo($address, $name = '')
    {
        $this->_to = $this->_makeAddress($address, $name);
    }

    public function setReplyTo($address, $name = '')
    {
        $this->_to = $this->_makeAddress($address, $name);
    }

    public function setSubject($text)
    {
        $this->_subject = $text;
    }

    public function setText($text)
    {
        $this->_text = $text;
    }

    public function addTmplVar($name, $value)
    {
        $this->_tmplVars[$name] = $value;
    }

    public function addFile($filePath, $fileName = '')
    {
        if (!$fileName) {
            $parts = pathinfo($filePath);
            $fileName = $parts['basename'];
        }
        $this->_files[] = array('path' => $filePath, 'name' => $fileName);
    }



    public function sendTextMail()
    {
        $this->_init();

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/plain; charset=iso-8859-1';
        $headers[] = 'Content-Transfer-Encoding: quoted-printable';
        $headers[] = 'From: ' . $this->_from;
        $headers[] = 'Reply-To: ' . $this->_replyTo;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $header    = implode("\r\n", $headers);
        //$options   =  '-f ' . $this->_from;

        mail($this->_to, utf8_decode($this->_subject), utf8_decode($this->_text), $header, $options);
    }

    public function sendHtmlMail()
    {
        $this->_init();

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: ' . $this->_from;
        $headers[] = 'Reply-To: ' . $this->_replyTo;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $header    = implode("\r\n", $headers);
        //$options   =  '-f ' . $this->_from;

        mail($this->_to, $this->_subject, $this->_text, $header, $options);
    }

    public function sendMailWithAttachment()
    {
        $this->_init();

        $semi_rand = md5(time());
        $mime_boundary = $semi_rand;

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . $this->_from;
        $headers[] = 'Reply-To: ' . $this->_replyTo;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $mime_boundary .'"';
        $headers[] = "This is a multi-part message in MIME format.";

        $headers[] = '--' . $mime_boundary;
        $headers[] = 'Content-Type: text/plain; charset=iso-8859-1';
        $headers[] = 'Content-Transfer-Encoding: quoted-printable';
        $headers[] = '';
        $headers[] = utf8_decode($this->_text . "\n");

        foreach($this->_files as $file) {
            $fd = fopen($file['path'], "rb");
            $data = fread($fd , filesize($file['path']));
            fclose($fd);
            $data = chunk_split(base64_encode($data));

            $headers[] = '--' . $mime_boundary;
            $headers[] = 'Content-Type: application/octet-stream; name="' . $file['name'] .'"';
            $headers[] = 'Content-Disposition: attachment; filename="' . $file['name'] .'"';
            $headers[] = 'Content-Transfer-Encoding: base64';
            $headers[] = '';
            $headers[] = $data;
	        $headers[] = '--' . $mime_boundary;
        }

        $header    = implode("\r\n", $headers);
        //$options   =  '-f ' . $this->_from;

        mail($this->_to, utf8_decode($this->_subject), '', $header, $options);
    }



    public function getDebugMail()
    {
        $this->_init();
        $mail =   'From: '      . $this->_from      . "\n"
                . 'To: '        . $this->_to        . "\n"
                . 'Reply-To: '  . $this->_replyTo   . "\n"
                . 'Subject: '   . $this->_subject   . "\n"
                . "Text:\n"     . $this->_text;

        $mail = str_replace('<', '&lang;', $mail);
        $mail = str_replace('>', '&rang;', $mail);
        return $mail;
    }

    private function _init()
    {
        $this->_from = $this->_replaceTmplVars($this->_from);
        $this->_to = $this->_replaceTmplVars($this->_to);
        $this->_subject = $this->_replaceTmplVars($this->_subject);
        $this->_text = $this->_replaceTmplVars($this->_text);

        if (!$this->_replyTo) {
            $this->_replyTo = $this->_from;
        }
    }

    private function _makeAddress($address, $name)
    {
        if ($name) {
            return $name . ' <' . $address . '>';
        } else {
            return $address;
        }
    }

    private function _replaceTmplVars($text)
    {
        foreach ($this->_tmplVars as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
