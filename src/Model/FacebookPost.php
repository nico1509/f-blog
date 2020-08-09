<?php


namespace Nico1509\Facebookblog\Model;


class FacebookPost {

    private string $message;
    private string $id;
    private \DateTime $createdTime;
    private ?string $imageSource;
    private ?string $imageLink;
    private ?string $videoSource;
    private ?string $videoLink;
    private ?string $link;

    public function __construct(
        string $message,
        string $id,
        \DateTime $createdTime,
        ?string $imageSource = null,
        ?string $imageLink = null,
        ?string $videoSource = null,
        ?string $videoLink = null
    ) {
        $this->message     = $message;
        $this->id          = $id;
        $this->createdTime = $createdTime;
        $this->imageSource = $imageSource;
        $this->imageLink   = $imageLink;
        $this->videoSource = $videoSource;
        $this->videoLink   = $videoLink;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage( string $message ): void {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId( string $id ): void {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedTime(): \DateTime {
        return $this->createdTime;
    }

    /**
     * @param \DateTime $createdTime
     */
    public function setCreatedTime( \DateTime $createdTime ): void {
        $this->createdTime = $createdTime;
    }

    /**
     * @return string|null
     */
    public function getImageSource(): ?string {
        return $this->imageSource;
    }

    /**
     * @param string|null $imageSource
     */
    public function setImageSource( ?string $imageSource ): void {
        $this->imageSource = $imageSource;
    }

    /**
     * @return string|null
     */
    public function getImageLink(): ?string {
        return $this->imageLink;
    }

    /**
     * @param string|null $imageLink
     */
    public function setImageLink( ?string $imageLink ): void {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string|null
     */
    public function getVideoSource(): ?string {
        return $this->videoSource;
    }

    /**
     * @param string|null $videoSource
     */
    public function setVideoSource( ?string $videoSource ): void {
        $this->videoSource = $videoSource;
    }

    /**
     * @return string|null
     */
    public function getVideoLink(): ?string {
        return $this->videoLink;
    }

    /**
     * @param string|null $videoLink
     */
    public function setVideoLink( ?string $videoLink ): void {
        $this->videoLink = $videoLink;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink( ?string $link ): void {
        $this->link = $link;
    }

}
