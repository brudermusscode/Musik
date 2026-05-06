<menu>
  <inr>
    <div height>
      <section>
        <moption request-get="album:edit" data-id="<?= $Album->id ?>">
          <mi>edit</mi>
          Editieren
        </moption>

        <form request="album:delete" responder=simple reload>
          <input type=hidden name=id value=<?= $Album->id ?> />
          <moption data-id="<?= $Album->id ?>" submit-closest>
            <mi>delete_forever</mi>
            Weg damit
          </moption>
        </form>
      </section>
    </div>
  </inr>
</menu>