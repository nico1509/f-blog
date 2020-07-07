<?php


namespace Nico1509\Facebookblog\Model;


class Log {

    /** @var string[] */
    private array $errors;

    /** @var int[] */
    private array $postIdsCreated;

    /** @var string */
    private string $date;

    /** @var string */
    private string $dataDir;

    public function __construct( string $dataDir ) {
        $this->errors         = [];
        $this->postIdsCreated = [];
        $date                 = new \DateTime();
        $this->date           = $date->format( 'Y-m-d_H-i-s' );
        $this->dataDir        = $dataDir;
    }

    /**
     * @param string $error
     */
    public function addError( string $error ): void {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * @param int $postId
     */
    public function addPostIdCreated( int $postId ): void {
        $this->postIdsCreated[] = $postId;
    }

    /**
     * @return string
     */
    public function getDate(): string {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDataDir(): string {
        return $this->dataDir;
    }
}
